<?php

namespace App\Modules\Api\Merchant;

use App\Models\EmailReceiver;
use App\Models\MerchantStaff;
use App\Models\SystemTicket;
use App\Modules\Api\Merchant\MerchantApiController;
use App\Modules\Api\Transformers\MailTransformer;
use Illuminate\Http\Request;
use App\Models\EmailStar;
use App\Http\Requests\SystemTicketFormRequest;
use Auth;
use Illuminate\Support\Facades\DB;

class MerchantMailApiController extends MerchantApiController
{
    protected $Transformer;

    public function __construct(MailTransformer $mailTransformer)
    {
        parent::__construct();
        $this->Transformer = $mailTransformer;
    }

    public function getalldata(Request $request){

        $merchantStaff = Auth::user();
        $inboxCount = MerchantStaff::email_receive($merchantStaff->id)
            ->select(\DB::raw("COUNT(*) as `count`"))
            ->whereNull('seen')
            ->first();

        $this->viewData['inboxCount'] = $inboxCount->count;

        $type = $this->headerdata('type');
        if((isset($type['type'])) && $type['type'] == 'sent'){
            $type = 'sent';
            $result = MerchantStaff::email_sent($merchantStaff->id,$request->q)
                ->with('sendermodel')->with('receiver')
                ->orderByDesc('id')->distinct();
        }elseif((isset($type['type'])) && $type['type'] == 'star'){
            $type = 'star';
            $result = MerchantStaff::email_star($merchantStaff->id)
                ->with('sendermodel')->with('receiver')
                ->orderByDesc('id')->distinct();

            if(!$result){
                $result= [];
            }
        }else{
            $type = 'inbox';
            $result = MerchantStaff::email_receive($merchantStaff->id,$request->q)
                ->with('sendermodel')->with('receiver')->with('parent')
                ->orderByDesc('id')->distinct();
        }

        $rows = $result->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no E-Mails to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('E-Mails to display'));

    }


    public function view($id,Request $request){
        // Parent function
        function parent($parentData) {
            static $data = [];
            if($parentData){
                $parentData->senderType = 'egpay';
                if($parentData->sendermodel instanceof Staff){
                    $parentData->senderType = 'staff';
                }elseif($parentData->sendermodel instanceof Merchant){
                    $parentData->senderType = 'merchant';
                }elseif($parentData->sendermodel instanceof MerchantStaff){
                    $parentData->senderType = 'merchant_staff';
                }


                // Receiver Type
                $parentData->receiverType = 'egpay';
                if($parentData->receivermodel instanceof Staff){
                    $parentData->receiverType = 'staff';
                }elseif($parentData->receivermodel instanceof Merchant){
                    $parentData->receiverType = 'merchant';
                }elseif($parentData->receivermodel instanceof MerchantStaff){
                    $parentData->receiverType = 'merchant_staff';
                }

                $data[] = $parentData;
                return parent($parentData->parent);
            }
            return $data;
        }

        $merchantStaff = Auth::user();

        $result = SystemTicket::select(['email.*','email_receiver.receivermodel_id','email_receiver.receivermodel_type','email_receiver.star','email_receiver.seen'])
            ->where('email.id',$id)
            ->where(function($query) use($merchantStaff){
                $query->where(function($query) use($merchantStaff) {
                    $query->where('email.sendermodel_type','App\\Models\\MerchantStaff');
                    $query->where('email.sendermodel_id',$merchantStaff->id);
                })
                    ->orWhere(function($query) use($merchantStaff) {
                        $query->where(function($query) use($merchantStaff){
                            $query->where('email_receiver.receivermodel_type','App\\Models\\MerchantStaff');
                            $query->where(function($query) use($merchantStaff) {
                                $query->where('email_receiver.receivermodel_id',$merchantStaff->id);
                            });
                        })
                        ->orWhere(function($query) use($merchantStaff){
                            $query->where('email_receiver.receivermodel_type','App\\Models\\Merchant');
                            $merchant = MerchantStaff::find($merchantStaff->id)->merchant;
                            $query->where(function($query) use($merchant) {
                                $query->where('email_receiver.receivermodel_id',$merchant->id);
                            });
                        });
                    });
            })
            ->leftjoin('email_receiver','email_receiver.email_id','=','email.id')
            ->first();

        if(!$result)
            return $this->respondNotFound(false,__('There Are no E-Mails to display'));


        if($request->star == 'true'){
            $getStarEmail = EmailStar::where('model_type',get_class(Auth::user()))
                ->where('model_id',$merchantStaff->id)
                ->where('email_id',$id)
                ->first();
            if($getStarEmail){
                $getStarEmail->delete();
                return $this->respondSuccess(false,__('This Email has ben Un-Starred'));
            }else{
                Auth::user()->email_star()->save(new EmailStar([
                    'email_id'=> $id
                ]));
                return $this->respondSuccess(false,__('This Email has ben Starred'));
            }
        }

        if($result->seen == null && ($result->receivermodel instanceof Staff || $result->receivermodel instanceof Merchant) ){
            $result->update(['seen'=> Carbon::now(),'seen_id'=>$merchantStaff->id]);
        }

        // Sender Type
        $result->senderType = 'egpay';
        if($result->sendermodel instanceof Staff){
            $result->senderType = 'staff';
        }elseif($result->sendermodel instanceof Merchant){
            $result->senderType = 'merchant';
        }elseif($result->sendermodel instanceof MerchantStaff){
            $result->senderType = 'merchant_staff';
        }

        $this->viewData['senderType'] = $result->senderType;

        $AllReceivers = $result->with('receiver')->get(['receivermodel_id','receivermodel_type']);
        $Receivers = [];
        foreach($AllReceivers as $oneReciver){
            $Receivers[] = (new $oneReciver->receivermodel_type)->find($oneReciver->receivermodel_id);
        }

        return $this->respondSuccess($this->Transformer->transform($result->toArray(),[$this->systemLang]),__('E-Mails to display'));

    }

    public function edit()
    {
        return back();
    }

    public function update()
    {
        return back();
    }

    public function destroy(SystemTicket $system_ticket,Request $request)
    {
        $id = $system_ticket->id;
        $merchantStaff = Auth::user();
        $result  = SystemTicket::where('id',$id)
            ->where(function($query) use($merchantStaff){
                $query->where(function($query) use($merchantStaff) {
                    $query->where('sendermodel_type','App\\Models\\Staff');
                    $query->where(function($query) use($merchantStaff) {
                        $query->where('sendermodel_id',$merchantStaff);
                        $query->orWhereNull('sendermodel_id');
                    });
                })
                    ->orWhere(function($query) use($merchantStaff) {
                        $query->where('receivermodel_type','App\\Models\\Staff');
                        $query->where(function($query) use($merchantStaff) {
                            $query->where('receivermodel_id',$merchantStaff);
                            $query->orWhereNull('receivermodel_id');
                        });
                    });
            })->first();

        if(!$result){
            return $this->setStatusCode(403)->respondWithError(false,__('You can\'t Delete this email'));
        }
        // Delete Data
        $system_ticket->delete();
            return $this->respondSuccess(false,__('Email has been deleted successfully'));

    }


    public function create(){
        return back();
    }


    public function store(SystemTicketFormRequest $request){
        $theRequest = $request->all();
        if($request->file('file')) {
            $theRequest['file'] = $request->file->store('system-tickets/'.date('y').'/'.date('m'));
        }

        if($request->reply_to){
            $dataFromReply = SystemTicket::findOrFail($request->reply_to);

            if($dataFromReply->sendermodel_type == 'App\Models\Staff'){
                $receiver['receivermodel_type'] = $dataFromReply->receivermodel_type;
                $receiver['receivermodel_id']   = $dataFromReply->receivermodel_id;
            }else{
                $receiver['receivermodel_type'] = $dataFromReply->sendermodel_type;
                $receiver['receivermodel_id']   = $dataFromReply->sendermodel_id;
            }

            $theRequest['parent_id'] = $request->reply_to;
        }else{
            if($request->receivermodel_id){
                $receiver['receivermodel_type'] = 'App\Models\MerchantStaff';
                $receiver['receivermodel_id']   = $request->receivermodel_id;
            }else{
                $receiver['receivermodel_type'] = 'App\Models\Merchant';
                $receiver['receivermodel_id']   = $request->merchant_id;
            }
        }

        $theRequest['sendermodel_type'] = get_class(Auth::user());
        $theRequest['sendermodel_id'] = Auth::id();


        $GLOBALS['status'] = false;
        DB::transaction(function () use ($theRequest,$receiver) {
            if(!$Email = SystemTicket::create($theRequest))
                return false;
            $receiver['email_id'] = $Email->id;
            if(!$Email->receiver()->create($receiver))
                return false;
            $GLOBALS['status'] = true;
        });


        if($GLOBALS['status'])
            return $this->respondCreated(false,__('Email successfully sent'));
        else{
            return $this->setStatusCode(403)->respondWithError(false,__('Email successfully sent'));

        }
    }
}
