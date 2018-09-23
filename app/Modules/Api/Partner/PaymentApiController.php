<?php

namespace App\Modules\Api\Partner;

use App\Libs\Payments\Payments;

use App\Models\Merchant;
use App\Models\MerchantStaff;
use App\Models\PaymentTransactions;

use App\Models\PaymentServiceProviderCategories;

use App\Modules\Api\Transformers\InquiryTransformer;

use App\Modules\Api\Transformers\PaymentTransformer;
use App\Modules\Api\Transformers\TransactionTransformer;

use App\Modules\Api\Transformers\ServiceTransformer;
use Illuminate\Http\Request;
use Auth;

use Illuminate\Support\Facades\Validator;

class PaymentApiController extends PartnerApiController
{
    protected $Transformer;
    protected $transactionTransformer;

    public function __construct(TransactionTransformer $transactionTransformer)
    {
        parent::__construct();
        $this->transactionTransformer = $transactionTransformer;
    }

    public function services(){

        $services = PaymentServiceProviderCategories::with('payment_service_providers')
            ->with('payment_service_providers.payment_services')
            ->get();

        $servicesTransformer = new ServiceTransformer();
        $services = $servicesTransformer->transform($services->toArray(),$this->systemLang);
        return $this->respondSuccess($services);

    }

    public function inquiry(Request $request)
    {
 
//        $x = 'a:4:{s:11:"@attributes";a:2:{s:6:"action";s:11:"Transaction";s:7:"version";s:1:"2";}s:6:"status";s:1:"0";s:10:"statusText";s:7:"Success";s:4:"data";a:5:{s:13:"transactionId";s:4:"9363";s:17:"transactionStatus";s:1:"2";s:8:"dateTime";s:14:"20180327100903";s:4:"info";s:46:"Balance: 147.2
//Min Value: 10
//Max Value: 10000
//";s:15:"ccTransactionId";s:16:"5192803311075887";}}
//';
//
//        $response = '{"status":true,"msg":"Successful","code":20006,"data":{"status":"0","transactionId":"9365","transactionStatus":2,"dateTime":"2018-03-27 10:32:51","ccTransactionId":"5192803311075889","info":{"payment_output_id":1,"amount":"147.2","min_value":"10","max_value":"10000","information":"Balance: 147.2\nMin Value: 10\nMax Value: 10000","en":[],"ar":[]},"service_info":{"service_id":192,"provider_id":10,"provider_name_ar":"\u0623\u0648\u0631\u0627\u0646\u062c \u062f\u064a \u0625\u0633 \u0625\u0644","service_name_ar":"\u062f\u0641\u0639 \u0641\u0627\u062a\u0648\u0631\u0629","provider_name_en":"Orange DSL","service_name_en":"Bill Payment","service_description_en":null,"service_description_ar":null,"merchant_id":"1-1"},"payment_by":{"name":"Bee","logo":"Bee"},"system_amount":{"status":true,"amount":"147.2","total_amount":151.19999999999998863131622783839702606201171875}},"service":{"0":{"id":210,"payment_service_id":192,"service_type":"payment","request_amount_input":"no","external_system_id":15,"service_name":"\u062f\u0641\u0639 \u0641\u0627\u062a\u0648\u0631\u0629","payment_service_api_parameters":[{"external_system_id":4,"value":"2548841552","payment_services_api_id":210,"position":1,"visible":"yes","required":"yes","type":"N","is_client_id":"yes","default_value":"0","min_length":9,"max_length":10,"name":"\u0631\u0642\u0645 \u0627\u0644\u0639\u0645\u064a\u0644"}]},"type":"payment","lang":{"button":"\u062f\u0641\u0639"}},"params":{"sln":"parameter_41"}}';
//
//        return $response;
        $inputs = $request->only([
            'service_id',
            'parameters'
        ]);

        if (!$inputs['parameters']) {
            $inputs['parameters'] = [];
        }


        $validator = Validator::make($inputs, [
            'service_id' => 'required|numeric|exists:payment_services,id',
            'parameters' => 'array'
        ]);

        if ($validator->errors()->any()) {

            return $this->ValidationError($validator, __('Validation Error'));
        }

        // Handle Params
      //  $inputs['parameters'] = array_column($inputs['parameters'], '1', '0');


        $adapter = Payments::selectAdapterByService($inputs['service_id']);

        $transformer = new InquiryTransformer();

        $response = $adapter::inquiry($inputs['parameters']);


        if (!$response['status']) {
            return $response;
            //return $this->respondWithError(false,'Service not available at the time');
        }


        $response['data']->param = self::GetPaymentTransaction($response['data']->transactionId);
        return $this->respondSuccess(
            array_merge($transformer->transform($response['data'], $this->systemLang), ['balance' => Auth::user()->paymentWallet()->first()->balance . ' ' . __('LE')])
        );

    }


    public function payment(Request $request)
    {

        $inputs = $request->only([
            'service_id',
            'parameters',
            'amount',
            'inquiry_transaction_id'
        ]);


      


        if (!$inputs['parameters']) {
            $inputs['parameters'] = [];
        }


        $adapter = Payments::selectAdapterByService($inputs['service_id']);

        if ((!$inputs['amount'] || $inputs['amount'] == '0.0') && (isset($inputs['service_id']))) {
            $data = $adapter::totalAmount($inputs['amount']);
            $inputs['amount'] = number_format($data['data']->amount, 2);
        }

        if (!$inputs['inquiry_transaction_id']) {
            $inquiry_transaction_id = false;
        } else {
            $inquiry_transaction_id = $inputs['inquiry_transaction_id'];
        }

        $validator = Validator::make($inputs, [
            'service_id' => 'required|numeric|exists:payment_services,id',
            'parameters' => 'array',
            //'amount'=> 'required|numeric'
        ]);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $transformer = new PaymentTransformer();

        // Handle Params
        $inputs['parameters'] = array_column($inputs['parameters'], '1', '0');


        $response = $adapter::payment($inputs['parameters'], $inquiry_transaction_id, $inputs['amount']);

        if (!$response['status']) {
            return $response;
            //  return $this->respondWithError(false,'Service not available at the time');
        }

        $response['data']->param = self::GetPaymentTransaction($response['data']->transactionId);
        return $this->respondSuccess(
            array_merge($transformer->transform($response['data'], $this->systemLang), ['balance' => Auth::user()->paymentWallet()->first()->balance . ' ' . __('LE')])
        );

    }


    public function getTotalAmount($serviceId, $amount)
    {
        $adapter = Payments::selectAdapterByService($serviceId);
        $makeRequest = $adapter::totalAmount($amount);

        return $makeRequest;
    }

    public function getTAmount(Request $request)
    {

        $validator = Validator::make($request->only(['service_id', 'amount']), [
            'service_id' => 'required|exists:payment_services,id',
            'amount' => 'required'
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $response = $this->getTotalAmount($request->service_id, $request->amount);
        if (!$response['status'])
            return $this->respondWithError(false, $response['msg']);
        $response['data']->total_amount = $response['data']->total_amount;
        return $this->respondSuccess($response['data']);

    }

    public function balance(Request $request){

        $merchant = Merchant::find(Auth::user()->id);

        if(!$wallet=$merchant->paymentWallet)
            return $this->respondNotFound(false,__('User Doesn\'t have Wallet'));

        return $this->respondSuccess(['balance'=>$wallet->balance]);
    }

    public function oneTransaction(Request $request){

        $inputs = $request->only(['transaction_id']);

        $validator = Validator::make($inputs, [
            'transaction_id' => 'required|numeric'

        ]);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $transaction = MerchantStaff::with(['PaymentTransactions'=>function($q) use($inputs,$request){
            $q->select(
                'id','model_id','model_type','service_type','payment_services_id','amount','total_amount','request_map','response_type'
            )
                ->where('id',$inputs['transaction_id'])
                ->where('model_id',$request->id)
                ->where('model_type','App\Models\MerchantStaff');
        }])->find($request->merchant_id);
        $transArray = ($transaction->toArray());

        if(empty($transArray['payment_transactions']))
           return $this->setCode(200)->respondWithError([],__('Wrong Transaction ID'));
        else
           return $this->respondSuccess($transArray['payment_transactions']);

    }



    private static function GetPaymentTransaction($transactionID){
        if($paymentTransaction = PaymentTransactions::find($transactionID))
            return array_map(function($val,$key){return ['name'=>str_replace('parameter_','',$key),'value'=>$val];},$paymentTransaction->request_map,array_keys($paymentTransaction->request_map));
        else
            return null;
    }



}