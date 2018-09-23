<?php
namespace App\Modules\Api\Merchant;

use App\Libs\Payments\Payments;
use App\Libs\WalletData;
use App\Models\Merchant;
use App\Models\PaymentInvoice;
use App\Models\PaymentServiceAPIs;
use App\Models\PaymentServiceProviderCategories;
use App\Models\PaymentServiceProviders;
use App\Models\PaymentServices;
use App\Models\PaymentTransactions;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Modules\Api\Transformers\InquiryTransformer;
use App\Modules\Api\Transformers\InvoiceTransformer;
use App\Modules\Api\Transformers\OneInvoiceTransformer;
use App\Modules\Api\Transformers\PaymentTransformer;
use App\Modules\Api\Transformers\TransactionTransformer;
use App\Modules\Api\Transformers\TransferTransformer;
use App\Modules\Api\Transformers\WalletTransactionsTransformer;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentApiController extends MerchantApiController
{
    protected $Transformer;
    protected $transactionTransformer;
    public function __construct(TransactionTransformer $transactionTransformer)
    {
        parent::__construct();
        $this->transactionTransformer = $transactionTransformer;
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

        $transformer = new InquiryTransformer();

        $response = $adapter::inquiry($inputs['parameters']);


        if(!$response['status']) {
            return $response;
            //return $this->respondWithError(false,'Service not available at the time');
        }

        $response['data']->param = self::GetPaymentTransaction($response['data']->transactionId);
        return $this->respondSuccess(
            array_merge($transformer->transform($response['data'],$this->systemLang),['balance'=>Auth::user()->paymentWallet()->first()->balance.' '.__('LE')])
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

        $adapter = Payments::selectAdapterByService($inputs['service_id']);

        if((!$inputs['amount'] || $inputs['amount'] == '0.0') && (isset($inputs['service_id']))){
            $data = $adapter::totalAmount($inputs['amount']);
            $inputs['amount'] = number_format($data['data']->amount,2);
        }

        if(!$inputs['inquiry_transaction_id']){
            $inquiry_transaction_id = false;
        }else{
            $inquiry_transaction_id = $inputs['inquiry_transaction_id'];
        }

        $validator = Validator::make($inputs,[
            'service_id'=> 'required|numeric|exists:payment_services,id',
            'parameters'=> 'array',
            //'amount'=> 'required|numeric'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $transformer = new PaymentTransformer();

        // Handle Params
        $inputs['parameters'] = array_column($inputs['parameters'],'1','0');




        $response = $adapter::payment($inputs['parameters'],$inquiry_transaction_id,$inputs['amount']);

        if(!$response['status']) {
            return $response;
            //return $this->respondWithError(false,'Service not available at the time');
        }

        $response['data']->param = self::GetPaymentTransaction($response['data']->transactionId);
        return $this->respondSuccess(
            array_merge($transformer->transform($response['data'],$this->systemLang),['balance'=>Auth::user()->paymentWallet()->first()->balance.' '.__('LE')])
        );

    }


    public function getTotalAmount($serviceId,$amount){
        $adapter = Payments::selectAdapterByService($serviceId);
        $makeRequest = $adapter::totalAmount($amount);

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
            return $this->respondWithError(false,$response['msg']);
        $response['data']->total_amount = $response['data']->total_amount;
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

    public function invoice(Request $request){
        $inputs = $request->only(['invoice_id','payment_transaction_id','payment_services_id','status','merchant_staff_id','lang']);
        $validator = Validator::make($inputs,[
            'invoice_id'                    => 'nullable|string',
            'payment_transaction_id'        => 'nullable|numeric',
            'payment_services_id'           => 'nullable|numeric',
            'status'                        => 'nullable|in:pending,paid,reverse',
            'merchant_staff_id'             => 'nullable|numeric',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }
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

        whereBetween($eloquentData,'DATE(payment_invoice.created_at)',$request->created_at1,$request->created_at2);

        if($request->invoice_id){
            $ids = explode(',',$request->invoice_id);
            $eloquentData->whereIn('payment_invoice.id',$ids);
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

        if($request->merchant_staff_id){
            $eloquentData->where('payment_transactions.model_id','=',$request->merchant_staff_id);
        }


//        whereBetween($eloquentData,'payment_transactions.amount',$request->amount1,$request->amount2);
//        whereBetween($eloquentData,'payment_transactions.total_amount',$request->total_amount1,$request->total_amount2);



        $rows = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();
        
        $transformer = new InvoiceTransformer();

        if(!$rows->items())
            return $this->respondNotFound(['balance'=>Auth::user()->paymentWallet()->first()->balance.' '.__('LE')],__('No reports to display'));

        return $this->respondSuccess(
            array_merge(
                $transformer->transformCollection($rows->toArray(),[$this->systemLang]),
                ['balance'=>Auth::user()->paymentWallet()->first()->balance.' '.__('LE')]
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
        $staff = Auth::user()->merchant()->MerchantStaff()->select(DB::raw("CONCAT(`firstname`,' ',`lastname`) as fullName"),'merchant_staff.id')->get()->toArray();

        return $this->respondSuccess(['items'=>$data->toArray(),'staff'=>$staff],'Done');
    }

    public function getDatabase(){
        // ------- START DATA
        $data['service_provider_categories'] = PaymentServiceProviderCategories::where('status','active')
            ->orderBy('sort_by','asc')
            ->get(['id','name_ar','name_en','description_ar','description_en','icon','sort_by'])
            ->toArray();



        $data['service_provider_categories'] = collect($data['service_provider_categories'])
            ->map(function ($value){
                $value['icon'] = imageResize($value['icon'],100,100);
                return $value;
            })
            ->toArray();



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


        $data['service_providers'] = collect($data['service_providers'])
            ->map(function ($value){
                $value['logo'] = imageResize($value['logo'],100,100);
                return $value;
            })
            ->toArray();



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


    public function transfer(Request $request){
        
//        return $this->respondWithError(false,__('Can not transfer at this time'));

        $inputs = $request->only(
            [
                'amount',
                'wallet_id',
                'wallet_id_confirmation'
            ]
        );

        $validator = Validator::make($inputs,[
            'wallet_id'     => 'required|numeric|exists:wallet,id|confirmed',
            'amount'        => 'required|numeric'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $OwnerWallet = Auth::user()->paymentWallet;
        $wallet = Wallet::where('id',$request->wallet_id)->with('walletowner')->first();

        if(!isset($OwnerWallet)){
            return $this->respondWithError(false,__('Your wallet not ready yet'));
        }

        if($inputs['amount'] > $OwnerWallet->balance){
            return $this->respondWithError(false,__('Not enough credit'));
        }

        if(($wallet->type != 'payment' || ($OwnerWallet->type != 'payment'))){
            return $this->respondWithError(false,__('Can not transfer to this wallet'));
        }

        WalletData::makeTransactionWithoutModel(true);
        $transfer = WalletData::makeTransaction(
            $inputs['amount'],
            'wallet',
            $OwnerWallet->id,
            $wallet->id,
            null,
            null,
            'App\Models\MerchantStaff',
            Auth::id(),
            'paid'
        );

        $Transformer = new TransferTransformer();
        if(!$transfer['status']) {
            if($transfer['code']==6)
                return $this->respondWithError($Transformer->transform($transfer, $this->systemLang), __('Can\'t transfer to yourself'));
            else
                return $this->respondWithError($Transformer->transform($transfer, $this->systemLang), __('Could not transfer at this time'));
        }

        if($transfer['status']) {
            $transfer['to_wallet'] = $wallet;
        }

        return $this->respondSuccess(
            array_merge($Transformer->transform($transfer,$this->systemLang),['balance'=>Auth::user()->paymentWallet()->first()->balance.' '.__('LE')])
        );

    }

    public function GetOneInvoice(Request $request){
        $inputs = $request->only('invoice_id');

        $validator = Validator::make($inputs,[
            'invoice_id'    => 'required|numeric|exists:payment_invoice,id',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $paymentInvoice = Auth::user()->merchant()->payment_invoice()->find($inputs['invoice_id']);
        $paymentTransaction = $paymentInvoice->payment_transaction()
            ->with(['payment_services'=>function($sql){
                $sql->with('payment_service_provider');
            }])->first();

        $adapter = Payments::selectAdapterByService($paymentTransaction->payment_services_id);

        $transformer = new OneInvoiceTransformer();
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
                $transformer->transform($response['data'],$this->systemLang),
                ['balance'=>Auth::user()->paymentWallet()->first()->balance.' '.__('LE')]
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