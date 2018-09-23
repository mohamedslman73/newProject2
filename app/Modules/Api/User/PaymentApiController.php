<?php
namespace App\Modules\Api\User;

use App\Libs\Payments\Payments;
use App\Libs\WalletData;
use App\Models\Merchant;
use App\Models\PaymentInvoice;
use App\Models\PaymentServiceAPIs;
use App\Models\PaymentServiceProviderCategories;
use App\Models\PaymentServiceProviders;
use App\Models\PaymentServices;
use App\Models\PaymentTransactions;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Modules\Api\Transformers\User\UserPaymentTransformer;


class PaymentApiController extends UserApiController
{
    protected $Transformer;
    protected $transactionTransformer;
    public function __construct(UserPaymentTransformer $userPaymentTransformer)
    {
        parent::__construct();
        $this->Transformer = $userPaymentTransformer;
    }

    public function inquiry(Request $request){
        $inputs = $request->only([
            'service_id',
            'parameters'
        ]);

        if(!$inputs['parameters']){
            $inputs['parameters'] = [];
        }


        $validator = Validator::make($inputs,[
            'service_id'=> 'required|numeric|exists:payment_services,id',
            'parameters'=> 'array'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        // Handle Params
        $inputs['parameters'] = array_column($inputs['parameters'],'1','0');


        $adapter = Payments::selectAdapterByService($inputs['service_id']);

        $response = $adapter::inquiry($inputs['service_id'],$inputs['parameters']);


        if(!$response['status']) {
            return $response;
            //return $this->respondWithError(false,'Service not available at the time');
        }

        $response['data']->param = self::GetPaymentTransaction($response['data']->transactionId);
        return $this->respondSuccess(
            array_merge(
                $this->Transformer->inquiryTransform($response['data'],$this->systemLang),
                ['balance'=>Auth::user()->eCommerceWallet->balance.' '.__('LE')]
            )
            );

    }

    public function payment(Request $request){
        $inputs = $request->only([
            'service_id',
            'parameters',
            'amount',
            'inquiry_transaction_id'
        ]);


        if(!$inputs['parameters']){
            $inputs['parameters'] = [];
        }

        if((!$inputs['amount'] || $inputs['amount'] == '0.0') && (isset($inputs['service_id']))){
            $data = $this->getTotalAmount($inputs['service_id'],0);
            $inputs['amount'] = number_format($data['data']->amount,2);
            //$inputs['amount'] = $data['data']->amount;
        }

        if(!$inputs['inquiry_transaction_id']){
            $inquiry_transaction_id = 0;
        }else{
            $inquiry_transaction_id = $inputs['inquiry_transaction_id'];
        }

        $validator = Validator::make($inputs,[
            'service_id'=> 'required|numeric|exists:payment_services,id',
            'parameters'=> 'array',
            'amount'=> 'required|numeric'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        // Handle Params
        $inputs['parameters'] = array_column($inputs['parameters'],'1','0');


        $adapter = Payments::selectAdapterByService($inputs['service_id']);

        $response = $adapter::payment($inputs['service_id'],$inputs['amount'],$inputs['parameters'],$inquiry_transaction_id);

        if(!$response['status']) {
            return $response;
            //return $this->respondWithError(false,'Service not available at the time');
        }

        $response['data']->param = self::GetPaymentTransaction($response['data']->transactionId);
        return $this->respondSuccess(
            array_merge($this->Transformer->paymentTransform($response['data'],$this->systemLang),
                ['balance'=>Auth::user()->eCommerceWallet()->first()->balance.' '.__('LE')])
        );

    }


    public function getTotalAmount($serviceId,$amount){
        $adapter = Payments::selectAdapterByService($serviceId);
        $makeRequest = $adapter::totalAmount($serviceId, $amount);

        return $makeRequest;
    }

    public function getTAmount(Request $request){

        $validator = Validator::make($request->only(['service_id','amount']),[
            'service_id'        =>'required|exists:payment_services,id',
            'amount'            =>'required'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $response = $this->getTotalAmount($request->service_id,$request->amount);
        if(!$response['status'])
            return $this->respondWithError(false,__('Could not calculate total amount'));

        $response['data']->total_amount = number_format($response['data']->total_amount,2);

        return $this->respondSuccess($response['data']);

    }



    private function inquiryParamToPaymentParm($param,$serviceid){
        $lang = $this->systemLang;
        $AllAparams = PaymentServiceAPIs::viewData($this->systemLang,[])
            ->where('payment_service_apis.payment_service_id','=',$serviceid)
            ->where('payment_service_apis.service_type','=','inquiry')
            ->with(['payment_service_api_parameters'=>function($sql)use($lang){
                $sql->select([
                    'external_system_id','payment_services_api_id','position',
                    'visible','required','type','is_client_id',
                    'default_value','min_length','max_length',
                    'name_'.$lang.' as name'])
                    ->orderBy('position')
                    ->where('visible','=','yes');
            }])
            ->get();

        $parameters = [];
        foreach($param as $key=>$val){
            $externalID = explode('_',$key)[1];
            $inqueryParam = $AllAparams->first()->payment_service_api_parameters->where('external_system_id','=',$externalID)->first();
            $name = strtolower($inqueryParam->name);
            if($name) {
                $parameters[$name] = $val;
            }
        }
        return $parameters;
    }


    public function walletTransactions(Request $request){
        $eloquentData = Auth::user()->eCommerceWallet->allTransaction();

        whereBetween($eloquentData,'created_at',$request->created_at1,$request->created_at2);
        if($request->status){
            $eloquentData->where('status',$request->status);
        }

        $eloquentData->where('creatable_type','=',get_class(Auth::user()));
        $eloquentData->where('creatable_id','=',Auth::id());

        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(['balance'=>Auth::user()->eCommerceWallet->balance.' '.__('LE')],__('No Wallet Transactions to display'));


        return $this->respondSuccess(
            array_merge(
                $this->Transformer->transformCollection($rows->toArray(),[$this->systemLang],'WalletTransactions'),
                ['balance'=>Auth::user()->eCommerceWallet()->first()->balance.' '.__('LE')]
            )
                ,__('Merchant Transactions details'));
    }


    public function invoice(Request $request){
        $lang = $this->systemLang;
        $eloquentData = Auth::user()->payment_invoice()
            ->with(['payment_transaction'=>function($query)use($lang){
            $query->with(['payment_services'=>function($query)use($lang){
                $query->select([
                    'id',
                    'name_'.$lang.' as name',
                    'icon',
                    'payment_service_provider_id'
                ]);

                $query->with(['payment_service_provider'=>function($query)use($lang){
                    $query->select([
                        'id',
                        'name_'.$lang.' as name',
                        'logo'
                    ]);
                }]);

            }]);
            $query->select([
                'id',
                'payment_services_id'
            ]);
        }])
            ->join('payment_transactions','payment_transactions.id','=','payment_invoice.payment_transaction_id')
            ->select([
                'payment_invoice.id',
                'payment_invoice.payment_transaction_id',
                'payment_invoice.total',
                'payment_invoice.total_amount',
                'payment_invoice.status',
                'payment_invoice.created_at'
            ])
            ->orderBy('id','DESC');

        whereBetween($eloquentData,'payment_invoice.created_at',$request->created_at1,$request->created_at2);

        if($request->invoice_id){
            $eloquentData->where('payment_invoice.id','=',$request->invoice_id3);
        }

        if($request->payment_transaction_id){
            $eloquentData->where('payment_invoice.payment_transaction_id','=',$request->payment_transaction_id);
        }

        if($request->payment_services_id){
            $eloquentData->where('payment_transactions.payment_services_id','=',$request->payment_services_id);
        }

        if($request->status){
            $eloquentData->where('payment_invoice.status','=',$request->status);
        }

//        whereBetween($eloquentData,'payment_transactions.amount',$request->amount1,$request->amount2);
//        whereBetween($eloquentData,'payment_transactions.total_amount',$request->total_amount1,$request->total_amount2);

        $rows = $eloquentData->jsonPaginate();

        if(!$rows->items())
            return $this->respondNotFound(['balance'=>Auth::user()->paymentWallet->balance.' '.__('LE')],__('No reports to display'));

        return $this->respondSuccess(
            array_merge(
                $this->Transformer->transformCollection($rows->toArray(),[$this->systemLang],'InvoiceTransformer'),
                ['balance'=>Auth::user()->eCommerceWallet()->first()->balance.' '.__('LE')]
            )
            ,__('Merchant Report'));

    }

    public function getUserServiceByTransaction(){
        $data = Auth::user()
            ->payment_invoice()
            ->join('payment_transactions','payment_transactions.id','=','payment_invoice.payment_transaction_id')
            ->join('payment_services','payment_services.id','=','payment_transactions.payment_services_id')
            ->join('payment_service_providers','payment_service_providers.id','=','payment_services.payment_service_provider_id')
            ->select([
                'payment_services.id',
                \DB::raw('CONCAT(payment_service_providers.name_'.$this->systemLang.'," - ",payment_services.name_'.$this->systemLang.') as name'),
            ])
            ->groupBy('payment_services.id')
            ->orderByRaw('count(payment_services.id) DESC')
            ->get();

        return $this->respondSuccess(['items'=>$data->toArray()],'Done');
    }

    public function getDatabase(){
        // ------- START DATA
        $data['service_provider_categories'] = PaymentServiceProviderCategories::where('status','active')
            ->get(['id','name_ar','name_en','description_ar','description_en','icon'])->toArray();

        $data['service_providers'] = PaymentServiceProviders::where('status','active')
            ->get([
                'id',
                'payment_service_provider_category_id as service_provider_category_id',
                'name_ar',
                'name_en',
                'description_ar',
                'description_en',
                'logo'
            ])->toArray();

        $services = PaymentServices::where('status','active')
            ->with(['payment_service_apis'=> function($data){
                $data->with('payment_service_api_parameters');
            }])
            ->get([
                'id',
                'payment_service_provider_id as service_provider_id',
                'name_ar',
                'name_en',
                'description_ar',
                'description_en',
                'request_amount_input',
                'icon'
            ]);


        $data['services'] = [];
        foreach ($services->toArray() as $key => $value){
            $data['services'][$key] = $value;
            unset($data['services'][$key]['payment_service_apis']);
        }
        $payment_service_apis = [];
        foreach ($services as $key => $value){
            foreach ($value->payment_service_apis as $VK => $VV){
                if($VV->payment_service_api_parameters->isEmpty()){
                    break;
                }
                foreach ($VV->payment_service_api_parameters as $BB){
                    $payment_service_apis[] = [
                        'id'=> $BB->id,
                        'service_type'=> $VV->service_type,
                        'external_system_id'=> $BB->external_system_id,
                        'payment_service_id'=> $value->id,
                        'name_ar'=> $BB->name_ar,
                        'name_en'=> $BB->name_en,
                        'position'=> $BB->position,
                        'visible'=> $BB->visible,
                        'required'=> $BB->required,
                        'type'=> $BB->type,
                        'is_client_id'=> $BB->is_client_id,
                        'default_value'=> $BB->default_value,
                        'min_length'=> $BB->min_length,
                        'max_length'=> $BB->max_length
                    ];
                }
            }
        }
        $data['service_parameters'] = $payment_service_apis;

        $data['options'] = [
            [
                'name'=> 'last_update',
                'value'=> $this->Date
            ]
        ];
        // -------- END DATA

        $outPut = str_replace([':null}',':null,'],[':""}',':"",'],json_encode($data));
        return $this->respondSuccess(json_decode($outPut));

    }




    public function GetOneInvoice(Request $request){
        $inputs = $request->only('invoice_id');

        $validator = Validator::make($inputs,[
            'invoice_id'    => 'required|numeric|exists:payment_invoice,id',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $paymentInvoice = Auth::user()->payment_invoice()->find($inputs['invoice_id']);
        $paymentTransaction = $paymentInvoice->payment_transaction()
            ->with(['payment_services'=>function($sql){
                $sql->with('payment_service_provider');
            }])->first();

        $adapter = Payments::selectAdapterByService($paymentTransaction->payment_services->id);


        $response = $adapter::ReviewTransaction($paymentTransaction);

        $response['data']->system_amount = [
            'amount'        =>      $paymentInvoice->total,
            'total_amount'        =>      $paymentInvoice->total_amount,
        ];

        if(!$response['status'])
            return $this->respondNotFound(false,__('Invoice not found'));

        $response['data']->param = self::GetPaymentTransaction($response['data']->transactionId);
        return $this->respondSuccess(
            array_merge(
                $this->Transformer->OneInvoice($response['data'],$this->systemLang),
                ['balance'=>Auth::user()->eCommerceWallet()->first()->balance.' '.__('LE')]
            )
        );

    }


    public function pretransfer(Request $request){
        $inputs = $request->only(['amount','username','username_confirmation']);
        $validator = Validator::make($inputs,[
            'username'      => 'required|numeric|exists:users,mobile|confirmed',
            'amount'        => 'required|numeric'
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }
        $data = User::where('mobile','=',$inputs['username'])->first();

        if($inputs['amount'] > Auth::user()->eCommerceWallet->balance){
            return $this->respondWithError(false,__('Not enough credit'));
        }

        if(!$data->eCommerceWallet){
            return $this->respondWithError(false,__('Can not transfer at this time'));
        }

        $Transformer = [
            'mobile'      =>      $data->mobile,
            //'name'      =>      $data->FullName,
            'id'        =>      $data->id
        ];

        return $this->respondSuccess($Transformer);

    }

    public function transfer(Request $request){
        $inputs = $request->only(['amount','username','username_confirmation']);
        $validator = Validator::make($inputs,[
            'username'      => 'required|exists:users,mobile|confirmed',
            'amount'        => 'required|numeric'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $data = User::where('mobile','=',$inputs['username'])->first();

        WalletData::makeTransactionWithoutModel(true);

        $transfer = WalletData::makeTransaction(
            $inputs['amount'],
            'wallet',
            Auth::user()->eCommerceWallet->id,
            $data->eCommerceWallet->id,
            null,
            null,
            'App\Models\User',
            Auth::id(),
            'paid'
        );


        if(!$transfer['status'])
            return $this->respondWithError($this->Transformer->Transfer($transfer,$this->systemLang),__('Could not transfer at this time'));

        if($transfer['status']) {
            $wallet = Wallet::select(['walletowner_id', 'walletowner_type'])
                ->where('id', '=', $transfer->to_id)
                ->with('walletowner')->first();
            $transfer['to_wallet'] = $wallet;
        }

        return $this->respondSuccess(
            array_merge($this->Transformer->Transfer($transfer,$this->systemLang),
                ['balance'=>Auth::user()->eCommerceWallet()->first()->balance.' '.__('LE')]
            )
        );

    }


    private static function GetPaymentTransaction($transactionID){
        if($paymentTransaction = PaymentTransactions::find($transactionID))
            return array_map(function($val,$key){return ['name'=>str_replace('parameter_','',$key),'value'=>$val];},$paymentTransaction->request_map,array_keys($paymentTransaction->request_map));
        else
            return null;
    }

}