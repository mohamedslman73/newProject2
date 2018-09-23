<?php
namespace App\Modules\Api\Merchant;
use App\Modules\Api\Transformers\NewsTransformer;
use Illuminate\Support\Facades\Auth;
use  Illuminate\Http\Request;

use App\Models\RequestBalance;
use Illuminate\Support\Facades\Validator;

class RequestBalanceApiController extends MerchantApiController {
    protected $Transformer;
    public function __construct(NewsTransformer $newsTransformer)
    {
        parent::__construct();
        $this->Transformer = $newsTransformer;
    }

    public function json($status,$msg = '', $data = [], $code = 200)
    {
        echo json_encode( ['status' => $status,'msg' => $msg, 'code' => $code, 'data' => (object)$data]);


    }
    public function requestBalance(Request $request)
    {
        $theRequest = $request->only([
            'amount',
            'description'
        ]);
        $validator = Validator::make($theRequest, [
            'amount' => 'required|numeric',
            'description' => 'required'
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $theRequest['staff_id']     = Auth::id();
        $theRequest['staff_balance_at_request']   = Auth::user()->paymentWallet->balance;

        if(RequestBalance::create($theRequest)) {

            if (!empty(setting('accountants_ids_notifications'))) {
                $monitorStaff = Staff::whereIn('id', explode("\n", setting('accountants_ids_notifications')))
                    ->get();
                foreach ($monitorStaff as $key => $value) {
                    $value->notify(
                        (new UserNotification([
                            'title' => 'Request Balance From: ' . Auth::user()->Fullname,
                            'description' => Auth::user()->Fullname . ' Request ' . amount($theRequest['amount'], true),
                            'url' => route('system.request-balance.index')
                        ]))
                            ->delay(5)
                    );
                }
            }

            return $this->json(true,__('Request Balance has been successfully Requested'));
        }else{
            return $this->json(false,__('Sorry Couldn\'t Request Balance'));
        }

    }
}