<?php

namespace App\Libs\Payments\Adapters;

use App\Libs\WalletData;
use App\Models\PaymentInvoice;
use App\Models\PaymentServiceAPIParameters;
use App\Models\PaymentServiceAPIs;
use App\Models\PaymentServiceProviderCategories;
use App\Models\PaymentServiceProviders;
use App\Models\PaymentServices;
use App\Models\PaymentTransactions;
use App\Models\Setting;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Requests;
use Requests_Hooks;
use Auth;
use App\Libs\Payments\Validator;
use Notification;
use App\Notifications\UserNotification;


/**
 * Trait BeeAdditionalFunction
 * @package App\Libs\Payments\Adapters
 */
trait BeeAdditionalFunction{
    /**
     * @var
     */
    private static $getServiceData;



    private static function roundDownAmount($amount){
        $explodedAmount = explode('.',$amount);
        if(count($explodedAmount) == 1){
            return $amount;
        }else{
            return (double)$explodedAmount[0].".".substr($explodedAmount[1],0,2);
        }
    }


    /**
     * @param $object
     * @return mixed
     */
    private static function object2array($object) { return @json_decode(@json_encode($object),1); }

    /**
     * @param $object
     * @return mixed
     */
    private static function array2object($object) { return @json_decode(@json_encode($object)); }

    /**
     * @param $serviceID
     * @param $serviceType
     * @param bool $updateMainValue
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    private static function getServiceData($serviceID, $serviceType, $updateMainValue = true){

        $getServiceData = PaymentServices::join('payment_service_apis','payment_service_apis.payment_service_id','=','payment_services.id')
            ->join('payment_service_providers','payment_service_providers.id','=','payment_services.payment_service_provider_id')
            ->join('payment_service_provider_categories', 'payment_service_provider_categories.id', '=','payment_service_providers.payment_service_provider_category_id' )
            ->where('payment_services.status','active')
            ->where('payment_service_providers.status','active')
            ->where('payment_service_apis.service_type','=',$serviceType)
            ->where('payment_services.id','=',$serviceID)
            ->select([
                \DB::raw('payment_services.*'),
                'payment_service_apis.id as api_id',
                'payment_service_apis.service_type',
                'payment_service_apis.external_system_id as api_external_system_id',
                'payment_service_apis.price_type as api_price_type',
                'payment_service_apis.service_value as api_service_value',
                'payment_service_apis.service_value_list as api_service_value_list',
                'payment_service_apis.min_value as api_min_value',
                'payment_service_apis.max_value as api_max_value',
                'payment_service_apis.commission_type as api_commission_type',
                'payment_service_apis.commission_value_type as api_commission_value_type',
                'payment_service_apis.fixed_commission as api_fixed_commission',
                'payment_service_apis.default_commission as api_default_commission',
                'payment_service_apis.from_commission as api_from_commission',
                'payment_service_apis.to_commission as api_to_commission',

                'payment_service_providers.id as payment_service_provider_id',
                'payment_service_provider_categories.id as payment_service_provider_category_id',

                // @TODO: Dynamic lang
                //'payment_service_providers.name_ar as payment_service_provider_name',
                //'payment_services.name_ar as payment_service_name',
                'payment_service_providers.name_ar as payment_service_provider_name_ar',
                'payment_services.name_ar as payment_service_name_ar',
                'payment_service_providers.name_en as payment_service_provider_name_en',
                'payment_services.name_en as payment_service_name_en',
                'payment_services.description_en as payment_service_description_en',
                'payment_services.description_ar as payment_service_description_ar'
            ])
            ->first();

        if($updateMainValue){
            self::$getServiceData = $getServiceData;
        }

        return $getServiceData;
    }

    /**
     * @param $status
     * @param $msg
     * @param $code
     * @param array $data
     * @return array
     */
    private static function outPut($status, $msg, $code, $data = []){
        return [
            'status'=> $status,
            'msg'=> (empty($msg)) ? '' : __((string)$msg),
            'code'=> $code,
            'data'=> (object) $data
        ];
    }

    /**
     * @param $APIID
     * @param $requestMap
     * @return array|bool
     */
    private static function validator($APIID, $requestMap){
        // Validate Parameters
        $parametersFromTable = PaymentServiceAPIParameters::where('payment_services_api_id',$APIID)->get();
        if(!$parametersFromTable){
            $parametersFromTable = [];
        }else{
            $parametersFromTable = $parametersFromTable->toArray();
        }

        $parameters = Validator::service($parametersFromTable,$requestMap);
        if($parameters !== true){
            return $parameters;
        }else{
            return true;
        }

        // Validate Parameters
    }

    /**
     * @param $clientAmount
     * @param $serviceType
     * @return array
     */
    private static function calculateTotalAmount($clientAmount, $serviceType){

        $data = self::$getServiceData;

        if(!$data){
            return [
                'status'=> false,
                'msg'=> __('Unknown Error')
            ];
        }

        // Calculate Amount
        if($serviceType == 'payment'){
            switch ($data->api_price_type){
                case 0:

                    if(
                        $clientAmount >= $data->api_min_value &&
                        $clientAmount <= $data->api_max_value
                    ){
                        $amount = $clientAmount;
                    }else{
                        return [
                            'status'=> false,
                            'msg'=> __('Amount must be between :from to :to',[
                                'from'=> $data->api_min_value,
                                'to'  => $data->api_max_value
                            ])
                        ];
                    }

                    break;

                case 1:
                    if($clientAmount == $data->api_service_value){
                        $amount = $data->api_service_value;
                    }else{
                        return [
                            'status'=> false,
                            'msg'=> __('Amount must be equal :amount',[
                                'amount'=> $data->api_service_value,
                            ])
                        ];
                    }
                    break;

                case 3:
                    if(in_array($clientAmount,explode(';',$data->api_service_value_list))){
                        $amount = $clientAmount;
                    }else{
                        return [
                            'status'=> false,
                            'msg'=> __('Amount must be one of this list :list',[
                                'list'=> $data->api_service_value_list
                            ])
                        ];
                    }

                    break;

                default:
                    return [
                        'status'=> false,
                        'msg'=> __('Unknown Error')
                    ];
                    break;
            }
        }else{
            $amount = $clientAmount;
            $getServiceDataForPayment = self::getServiceData($data->id,'payment',false);
            if(!$getServiceDataForPayment){
                return [
                    'status'=> false,
                    'msg'=> __('Payment Amount Error')
                ];
            }

            $data = $getServiceDataForPayment;
        }
        // Calculate Amount

        // Calculate Total Amount
        switch ($data->api_commission_type){
            case 0:
                $totalAmount = $amount;
                break;

            case 1:
                if($data->api_commission_value_type == 0){
                    $totalAmount = $amount*(1+($data->api_fixed_commission/100));
                }elseif($data->api_commission_value_type == 1){
                    $totalAmount = $amount+$data->api_fixed_commission;
                }else{
                    return [
                        'status'=> false,
                        'msg'=> __('Amount Error')
                    ];
                }
                break;
        }
        // Calculate Total Amount

        return [
            'status'=> true,
            'amount'=> self::roundDownAmount($amount),
            'total_amount'=> self::roundDownAmount($totalAmount)
        ];

    }





    /**
     * @param $serviceID
     * @param $amount
     * @param $totalAmount
     * @param $APIExternalSystemID
     * @param $serviceType
     * @return mixed
     */
    private static function createPaymentTransaction($serviceID, $amount, $totalAmount, $APIExternalSystemID, $serviceType){
        return Auth::user()->PaymentTransactions()->create([
            'payment_services_id'=> $serviceID,
            'amount'=> $amount,
            'total_amount'=> $totalAmount,
            'request_map'=> Validator::$parametersToSDK,
            'external_system_id'=> $APIExternalSystemID,
            'service_type'=> $serviceType,
            'ip'            => getRealIP()
        ]);
    }

    // Handle Data

    /**
     * @param $data
     * @return int
     */
    private static function handleAmount($data){

        if(isset($data['information'])){
            return $data['information'];
        }elseif(isset($data['due_amount'])){
            return $data['due_amount'];
        }elseif(isset($data['amount'])){
            return $data['amount'];
        }elseif(isset($data['default_amount'])){
            return $data['default_amount'];
        }elseif(isset($data['invoice_total_due_amount'])){
            return $data['invoice_total_due_amount'];
        }elseif(isset($data['balance'])){
            return $data['balance'];
        }

        return 0;
    }

    /**
     * @param $data
     * @return int
     */
    private static function handleMinValue($data){
        if(isset($data['min_value'])){
            return $data['min_value'];
        }elseif(isset($data['min'])){
            return $data['min'];
        }elseif(isset($data['range_min_amount'])){
            return $data['range_min_amount'];
        }elseif(isset($data['min_amount'])){
            return $data['min_amount'];
        }

        return 0;
    }

    /**
     * @param $data
     * @return int
     */
    private static function handleMaxValue($data){
        if(isset($data['max_value'])){
            return $data['max_value'];
        }elseif(isset($data['max'])){
            return $data['max'];
        }elseif(isset($data['range_max_amount'])){
            return $data['range_max_amount'];
        }elseif(isset($data['max_amount'])){
            return $data['max_amount'];
        }

        return 0;
    }
    // Handle Data

    /**
     * @param $action
     * @param $response
     * @param $serviceData
     * @return array
     */
    private static function handleResponse($action, $response, $serviceData,$paymentTransaction = null){
        if (!$response) {
            return self::outPut(false, 'Service not available', 20001);
        }

        $return = [];
        $return['status'] = (string) $response->status;

        // Handle Response Status
        switch ($return['status']){

            case '20009':
                $serviceList = self::serviceList();


//                setError(collect($serviceList)->toArray(),null,null);

                Setting::where('name','payment_bee_service_version')
                    ->update(['value'=> (int)$serviceList->data->serviceVersion]);

                if($paymentTransaction && !empty(setting('monitor_staff'))){
                    $monitorStaff = Staff::whereIn('id',explode("\n",setting('monitor_staff')))
                        ->get();

                    foreach ($monitorStaff as $key => $value){
                        $value->notify(
                            (new UserNotification([
                                'title'         => 'Bee Error: '.$return['status'],
                                'description'   => (string)$response->statusText.'| Transaction #ID:'.$paymentTransaction->id,
                                'url'           => route('payment.transactions.list',['id'=>$paymentTransaction->id])
                            ]))
                                ->delay(5)
                        );
                    }
                }
                return self::outPut(false, 'Service not available7', 20002);

                break;
            case '11000':
                return self::outPut(false, 'Service not available9', 20003);
                break;
            case '11001':
            case '11002':
                // Update Service Commission
                $serviceListData = self::serviceList();

                if($serviceListData){
                    foreach ($serviceListData->data->serviceList->service as $key => $value){
                        if($value->accountId == $serviceData->api_external_system_id){
                            PaymentServiceAPIs::where('id',$serviceData->api_id)
                                ->update([
                                    'price_type'=> (string) $value->priceType,
                                    'service_value'=> (string) $value->serviceValue,
                                    'service_value_list'=> (string) $value->serviceValueList,
                                    'min_value'=> (string) $value->minValue,
                                    'max_value'=> (string) $value->maxValue,
                                    'commission_type'=> (string) $value->commissionType,
                                    'commission_value_type'=> (string) $value->commissionValueType,
                                    'fixed_commission'=> (string) $value->fixedCommission,
                                    'default_commission'=> (string) $value->defaultCommission,
                                    'from_commission'=> (string) $value->fromCommission,
                                    'to_commission'=> (string) $value->toCommission
                                ]);
                            break;
                        }
                    }
                }
                if($paymentTransaction && !empty(setting('monitor_staff'))){
                    $monitorStaff = Staff::whereIn('id',explode("\n",setting('monitor_staff')))
                        ->get();

                    foreach ($monitorStaff as $key => $value){
                        $value->notify(
                            (new UserNotification([
                                'title'         => 'Bee Error: '.$return['status'],
                                'description'   => (string)$response->statusText.'| Transaction #ID:'.$paymentTransaction->id,
                                'url'           => route('payment.transactions.list',['id'=>$paymentTransaction->id])
                            ]))
                                ->delay(5)
                        );
                    }
                }
                return self::outPut(false, 'Please Make a new request', 11002);
                break;
            case '11003':
            case '20000':
            case '20001':
            case '20002':
            case '20003':
            case '20004':
            case '20005':
            case '20006':
            case '20007':
            case '20008':
            case '20010':
/*                if($paymentTransaction && !empty(setting('monitor_staff'))){
                    $monitorStaff = Staff::whereIn('id',explode("\n",setting('monitor_staff')))
                        ->get();

                    foreach ($monitorStaff as $key => $value){
                        $value->notify(
                            (new UserNotification([
                                'title'         => 'Bee Error: '.$return['status'],
                                'description'   => (string)$response->statusText.'| Transaction #ID:'.$paymentTransaction->id,
                                'url'           => route('payment.transactions.list',['id'=>$paymentTransaction->id])
                            ]))
                                ->delay(5)
                        );
                    }
                }*/

                return self::outPut(false, 'Service not available', 20003);
            break;
        }

        // Start Handel Data
        switch ($action) {
            case 'inquiry':
            case 'Transaction':
            case 'TransactionStatus':

                // Handle Transaction Response Status
                switch ((string) $response->data->transactionStatus){
                    case '3':
                    case '4':
                    case '5':
                        /*if($paymentTransaction && !empty(setting('monitor_staff'))){
                            $monitorStaff = Staff::whereIn('id',explode("\n",setting('monitor_staff')))
                                ->get();

                            foreach ($monitorStaff as $key => $value){
                                $value->notify(
                                    (new UserNotification([
                                        'title'         => 'Bee Transaction Error: '.(string) $response->data->transactionStatus,
                                        'description'   => 'Error Transaction Response Error #ID:'.$paymentTransaction->id,
                                        'url'           => route('payment.transactions.list',['id'=>$paymentTransaction->id])
                                    ]))
                                        ->delay(5)
                                );
                            }
                        }*/

                        return self::outPut(false, $response->data->info, 20004);

                    break;
                }



                $explodeDate = trim($response->data->dateTime);

                $return['transactionId'] = trim($response->data->transactionId);
                $return['transactionStatus'] = (int)trim($response->data->transactionStatus);
                $return['dateTime'] = substr($explodeDate, 0, 4)
                    . '-'
                    . substr($explodeDate, 4, 2)
                    . '-'
                    . substr($explodeDate, 6, 2)
                    . ' '
                    . substr($explodeDate, 8, 2)
                    . ':'
                    . substr($explodeDate, 10, 2)
                    . ':'
                    . substr($explodeDate, 12, 2);

                $return['ccTransactionId'] = trim($response->data->ccTransactionId);

                // ----- INFO DATA
                $return['info'] = [];
                $return['info']['payment_output_id'] = $serviceData->payment_output->id;

                $outPutParameters = array_column($serviceData->payment_output->parameters, 'language', 'key');

                $beforeExplode = trim($response->data->info);
                $text = explode("\n", $beforeExplode);

                if (empty($text)) {
                    $return['info']['amount'] = '0';
                    $return['info']['total_amount'] = '0';
                    $return['info']['min_value'] = '0';
                    $return['info']['max_value'] = '0';
                    $return['info']['information'] = $text;
                } else {
                    $infoData = [];

                    foreach ($text as $key => $value) {
                        $split      = explode(':', $value);
                        $countSplit = count($split);

                        if (empty($split) || !$countSplit) {
                            continue;
                        }elseif($countSplit == 1){
                            $infoData[str_repeat('-',$key+1)] = trim($split[0]);
                            continue;
                        }elseif($countSplit > 2){
                            $newSplit = $split;
                            unset($newSplit[0]);
                            $split[1] = implode(':',$newSplit);
                        }

                        $infoData[snake_case(camel_case($split[0]))] = trim($split[1]);
                    }


                    $infoAllData = $infoData;

                    $newInfoData                = [];
                    $newInfoData['amount']      = self::handleAmount($infoData);
                    $newInfoData['min_value']   = self::handleMinValue($infoData);
                    $newInfoData['max_value']   = self::handleMaxValue($infoData);
                    $newInfoData['information'] = $beforeExplode;

                    if($newInfoData['amount'] == 0 && $action == 'inquiry'){
                        return self::outPut(false, 'لا يوجد فواتير مستحقة', 20001);
                    }

                    foreach ($infoData as $key => $value) {
                        if (isset($outPutParameters[$key])) {
                            foreach ($outPutParameters[$key] as $languageKey => $language) {
                                $newInfoData[$languageKey][] = [
                                    'key'  => $language,
                                    'value'=> trim($value)
                                ];
                            }
                        }
                    }


                    // If there are No output
                    if (!isset($newInfoData['ar'])) {

                        // Send Notification
                        /*if($paymentTransaction && !empty(setting('monitor_staff'))){
                            $monitorStaff = Staff::whereIn('id',explode("\n",setting('monitor_staff')))
                                ->get();

                            foreach ($monitorStaff as $key => $value){
                                $value->notify(
                                    (new UserNotification([
                                        'title'         => 'Bee Error: No Payment Output',
                                        'description'   => (string)$response->statusText.'| Transaction #ID:'.$paymentTransaction->id,
                                        'url'           => route('payment.transactions.list',['id'=>$paymentTransaction->id])
                                    ]))
                                        ->delay(5)
                                );
                            }
                        }*/


                        $BeeText = [];
                        foreach ($infoAllData as $key => $value) {
                            if (in_array($key,[
                                    // Min
                                    'min_value',
                                    'min',
                                    'range_min_amount',
                                    'min_amount',

                                    // Max
                                    'max_value',
                                    'max',
                                    'range_max_amount',
                                    'max_amount',

                                    // Amount
                                    'information',
                                    'due_amount',
                                    'amount',
                                    'default_amount',
                                    'invoice_total_due_amount',
                                    'balance'
                                ])
                            ) {
                                continue;
                            }


                            if(empty(trim(str_replace('-','',$key)))){
                                $key = __('Details');
                            }

                            $BeeText[] = [
                                'key'   => trim($key),
                                'value' => trim($value)
                            ];
                        }
                        $newInfoData['ar'] = $newInfoData['en'] = $BeeText;

                    }


                    $return['info'] = $return['info'] + $newInfoData;
                }

                break;

            case 'GetBalance':
                $return['balance'] = trim($response->data->balance);
                break;

            case 'ServiceList':
                $return['serviceVersion'] = trim($response->data->serviceVersion);

                $i = 0;
                $providerGroup = [];
                foreach ($response->data->providerGroupList->providerGroup as $key => $value) {
                    $providerGroup[$i++] = (array)$value;
                }
                $return['providerGroup'] = $providerGroup;

                $i = 0;
                $provider = [];
                foreach ($response->data->providerList->provider as $key => $value) {
                    $provider[$i++] = (array)$value;
                }
                $return['provider'] = $provider;

                $i = 0;
                $service = [];
                foreach ($response->data->serviceList->service as $key => $value) {
                    $newI = $i++;
                    $service[$newI] = (array)$value;
                    $service[$newI]['serviceValue'] = (string)$value->serviceValue;
                    $service[$newI]['serviceValueList'] = (string)$value->serviceValueList;
                    $service[$newI]['minValue'] = (string)$value->minValue;
                    $service[$newI]['maxValue'] = (string)$value->maxValue;
                    $service[$newI]['fixedCommission'] = (string)$value->fixedCommission;
                    $service[$newI]['defaultCommission'] = (string)$value->defaultCommission;
                    $service[$newI]['fromCommission'] = (string)$value->fromCommission;
                    $service[$newI]['toCommission'] = (string)$value->toCommission;
                }
                $return['service'] = $service;


                $i = 0;
                $serviceInputParameter = [];
                foreach ($response->data->serviceInputParameterList->serviceInputParameter as $key => $value) {
                    $newI = $i++;
                    $serviceInputParameter[$newI] = (array)$value;
                    $serviceInputParameter[$newI]['defaultValue'] = (string)$value->defaultValue;
                }
                $return['serviceInputParameter'] = $serviceInputParameter;

                break;

            default:
                return self::outPut(false, 'Service not available10', 20005);
                break;
        }


        $return['service_info'] = [
            // @TODO : set real merchant ID or staff id
            // 'merchant_id'=> Auth()->user()->merchant()->id,
            'service_id'      => $serviceData->id,
            'service_provider_category_id' => $serviceData->payment_service_provider_category_id,
            'provider_id'     => $serviceData->payment_service_provider_id,
            'provider_name_ar'=> $serviceData->payment_service_provider_name_ar,
            'service_name_ar' => $serviceData->payment_service_name_ar,
            'provider_name_en'=> $serviceData->payment_service_provider_name_en,
            'service_name_en' => $serviceData->payment_service_name_en,
            'service_description_en' => $serviceData->payment_service_description_en,
            'service_description_ar' => $serviceData->payment_service_description_ar
        ];


        $return['payment_by'] = [
            'name'=> 'Bee',
            'logo'=> 'Bee'
        ];

        return self::outPut(true,'Successful',20006,$return);

    }

    /**
     * @param $action
     * @param array $data
     * @param $locale
     * @return mixed
     */
    private static function generateXMLRequest($action, array $data, $locale){

        if(isset($data['serviceData'])){
            unset($data['serviceData']);
        }

        if($action == 'Transaction'){
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Request action="'.$action.'" version="2"/>');
        }else{
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Request action="'.$action.'" version="1"/>');
        }

        $xml->addChild('login', "8985012331");
        $xml->addChild('password', "!@#ASDKAS$0101542365");
        $xml->addChild('locale', $locale);
        $xml->addChild('terminal_id', 1);

        if(!empty($data)){
            $xmlData = $xml->addChild('data');
            foreach ($data as $key => $value){
                if($key == 'requestMap') continue;
                $xmlData->addChild($key,$value);
            }


            if($action == 'Transaction'){

                $requestMap = $xmlData->addChild('requestMap');
                if(isset($data['requestMap']) && !empty($data['requestMap'])){
                    foreach ($data['requestMap'] as $key => $value){
                        $item = $requestMap->addChild('item');
                        $item->addChild('key',substr($key,10));
                        $item->addChild('value',$value);
                    }
                }
            }
        }
        return $xml->asXML();
    }

}


/**
 * Class Bee
 * @package App\Libs\Payments\Adapters
 */
class Bee implements \App\Libs\Payments\PaymentInterface{

    use BeeAdditionalFunction;

    /**
     * @var int
     */

    private static $serviceVersion = 176;
    public  static $serviceID = null;

    /**
     * @param $action
     * @param array $data
     * @param string $locale
     * @return bool|\SimpleXMLElement
     */
    private static function makeRequest($action, array $data, $locale = 'en',$returnType = 'normal'){
        $generateXMLRequest = static::generateXMLRequest($action,$data,$locale);
        $BeeUrl = 'https://merchant.bee.com.eg:7443/xmlgw/action';

        // Rrequests Hooks
        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_request', function($handle){
            curl_setopt($handle, CURLOPT_FAILONERROR, true);
            curl_setopt($handle, CURLOPT_SSLVERSION, 3);
            curl_setopt($handle, CURLOPT_VERBOSE, true);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_HEADER , false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        });

        try{
            $response = Requests::post($BeeUrl,['Content-Type' => 'text/xml'],$generateXMLRequest,['hooks'=> $hooks,'timeout'=>1000,'connect_timeout'=>1000]);
            if($returnType == 'XML'){
                return $response->body;
            }else{
                return simplexml_load_string($response->body,'SimpleXMLElement',LIBXML_NOCDATA);
            }
        }catch (\Exception $exception){
            if($action == 'Transaction'){
                return static::transactionStatus($data['transactionId']);
            }
            return false;
        }

    }

    /**
     * @param $serviceID
     * @param array $requestMap
     * @return array
     */
    public static function inquiry($requestMap = []){

        $serviceID = self::$serviceID;

        // Check Service Data
        $getServiceData = self::getServiceData($serviceID,'inquiry');

        if(!$getServiceData || !$getServiceData->payment_sdk || !$getServiceData->payment_service_provider){
            return self::outPut(false,'Service not available',401);
        }

        // Validate Request Map
        $validator = self::validator($getServiceData->api_id,$requestMap);
        if($validator !== true){
            return self::outPut(false,'Verification Error',103,$validator);
        }

        //  CreatePayment Transaction
        $paymentTransaction = self::createPaymentTransaction($getServiceData->id,0,0,$getServiceData->api_external_system_id,'inquiry');

        if(!$paymentTransaction){
            return self::outPut(false,'Service not available',402);
        }

        // Make Inquiry using Bee server
        $response = static::makeRequest(
            'Transaction',
            [
                'serviceVersion'=> setting('payment_bee_service_version'),
                'transactionId'=> $paymentTransaction->id, // From Our System
                'serviceAccountId'=> $getServiceData->api_external_system_id,
                'amount'=> 0,
                'totalAmount'=> 0,
                'requestMap'=> $paymentTransaction->request_map,
            ]
        );

        $handleResponse = self::handleResponse('inquiry',$response,$getServiceData,$paymentTransaction);

        // Handle Amount
        if(isset($handleResponse['data']->info['amount'])){
            $handleResponse['data']->system_amount = self::calculateTotalAmount($handleResponse['data']->info['amount'],'inquiry');
        }else{
            $handleResponse['data']->system_amount = self::calculateTotalAmount(0,'inquiry');
        }


        if(!$handleResponse['status'] || !$handleResponse['data']->system_amount['status']){
            $paymentTransaction->update([
                'amount'=> $handleResponse['data']->system_amount['amount'],
                'total_amount'=> $handleResponse['data']->system_amount['total_amount'],
                'response_type'=> 'fail',
                'response'=> self::object2array($response)
            ]);
            return self::outPut(false,$handleResponse['msg'],$handleResponse['code']);
        }

        $paymentTransaction->update([
            'amount'=> $handleResponse['data']->system_amount['amount'],
            'total_amount'=> $handleResponse['data']->system_amount['total_amount'],
            'response_type'=> 'done',
            'response'=> self::object2array($response)
        ]);

        return $handleResponse;

    }

    /**
     * @param int $paymentServiceID
     * @param array $inquiryRequestMapData
     * @param array $paymentRequestMapData
     * @return array
     * @internal param $serviceID
     * @internal param $amount
     * @internal param array $requestMap
     * @internal param int $inquiryTransactionsID
     */


    public static function handleParameters(int $paymentAPIID,array $inquiryRequestMapData,array $paymentRequestMapData){

        $inquiryRequestMapIDs = str_replace('parameter_','',array_keys($inquiryRequestMapData));
        if(!$inquiryRequestMapIDs){
            return $paymentRequestMapData;
        }

        // Get external_system_id
        $inquiryParametersData = PaymentServiceAPIParameters::whereIn('external_system_id',$inquiryRequestMapIDs)
            ->get([
                'external_system_id',
                'name_en'
            ]);

        if($inquiryParametersData->isEmpty()){
            return $paymentRequestMapData;
        }

        $allPaymentParametersData = PaymentServiceAPIParameters::where('payment_services_api_id',$paymentAPIID)->get([
            'external_system_id',
            'name_en'
        ]);

        foreach ($allPaymentParametersData as $key => $value){
            foreach ($inquiryParametersData as $key2 => $value2){
                if( trim(strtolower($value2->name_en)) == trim(strtolower($value->name_en)) ){
                    $paymentRequestMapData['parameter_'.$value->external_system_id] = $inquiryRequestMapData['parameter_'.$value2->external_system_id];
                }
            }
        }

        return $paymentRequestMapData;
    }


    public static function payment($requestMap = [], $inquiryTransactionsID = false,$amount = false){

        $serviceID = self::$serviceID;

        if(!$inquiryTransactionsID && !$amount){
            return self::outPut(false,'Service not available',20007);
        }

        // Check Service Data
        $getServiceData = self::getServiceData($serviceID,'payment');
        if(!$getServiceData || !$getServiceData->payment_sdk || !$getServiceData->payment_service_provider){
            return self::outPut(false,'Service not available2',20008);
        }

        if($getServiceData->request_amount_input == 'yes'){
            if($amount == '0' || $amount == '0.0')
                return self::outPut(false,'Amount can not be empty or 0',20009);
        }

        $getServiceDataForInquiry = self::getServiceData($getServiceData->id,'inquiry',false);
        if($getServiceDataForInquiry && !$inquiryTransactionsID){
            return self::outPut(false,'Unknown Error',20010);
        }

        // Get Amount From Inquiry
        if($inquiryTransactionsID){

            $inquiryTransaction = PaymentTransactions::where('id',$inquiryTransactionsID)
                ->where('response_type','done')
                ->where('is_paid','no')
                ->whereNotNull('response')
                ->first();

            if(!$inquiryTransaction ||  empty($inquiryTransaction->response)){
                return self::outPut(false,'Unknown Error',20011);
            }

            $inquiryResponse    = self::handleResponse('inquiry',self::array2object($inquiryTransaction->response),$getServiceDataForInquiry);
            $amount             = $inquiryResponse['data']->info['amount'];

            $requestMap = self::handleParameters($getServiceData->api_id,$inquiryTransaction->request_map,$requestMap);
        }

        $amountData = self::calculateTotalAmount($amount,'inquiry');

        if(!$amountData['status']){
            return self::outPut(false,'Amount Error',103,$amountData);
        }

        // Payment Wallet Object Update: 2017-12-07
        $paymentWalletObject = WalletData::getWalletByUserData(Auth::user()->modelPath,Auth::id(),'payment');

        $getWalletBalance    = WalletData::balance($paymentWalletObject);
        if($amountData['total_amount'] > $getWalletBalance){
            return self::outPut(false,'You do not have enough credit to make this transaction',103);
        }

        // Validate Request Map
        $validator = self::validator($getServiceData->api_id,$requestMap);
        if($validator !== true){
            return self::outPut(false,'Verification Error',103,$validator);
        }

        // Create Payment Transaction
        $paymentTransaction = new \stdClass;
        $payment_invoice    = new \stdClass;
        $transactionData    = new \stdClass;

        try{
            DB::beginTransaction();

            $paymentTransaction = self::createPaymentTransaction($getServiceData->id, $amountData['amount'], $amountData['total_amount'], $getServiceData->api_external_system_id, 'payment');
            $payment_invoice = Auth::user()->payment_invoice()->create([
                'payment_transaction_id' => $paymentTransaction->id,
                'total' => $amountData['amount'],
                'total_amount' => $amountData['total_amount'],
                'status' => 'pending'
            ]);

            $transactionData = WalletData::makeTransaction(
                $amountData['total_amount'],
                'wallet',
                $paymentWalletObject,
                setting('payment_wallet_id'),
                'invoice',
                $payment_invoice->id,
                Auth::user()->modelPath,
                Auth::id()
            );

            if(!$transactionData || (is_array($transactionData) && !$transactionData['status'] ) ){
                throw new \Exception('Transaction Error');
            }

        }catch (\PDOException $e){
            DB::rollBack();
            return self::outPut(false,'Service not available3',20012);
        }


        try{
            // Make Inquiry using Bee server
            $response = static::makeRequest(
                'Transaction',
                [
                    'serviceVersion'=> setting('payment_bee_service_version'),
                    'transactionId'=> $paymentTransaction->id, // From Our System
                    'serviceAccountId'=> $getServiceData->api_external_system_id,
                    'amount'=> $amountData['amount'],
                    'totalAmount'=> $amountData['total_amount'],
                    'requestMap'=> $paymentTransaction->request_map,
                ]
            );

        }catch (\Exception $e){
            DB::rollBack();
            return self::outPut(false,'Service not available4',20013);
        }

        DB::commit();

        $handleResponse = self::handleResponse('Transaction',$response,$getServiceData);

        // If Provider is Down
        if($response === false){
            $paymentTransaction->update([
                'response_type'=> 'fail',
                'response'=> self::object2array($response)
            ]);
            return $handleResponse;
        }
        // If Provider is Down


        if(!$handleResponse['status']){
            $paymentTransaction->update([
                'response_type'=> 'fail',
                'response'=> self::object2array($response)
            ]);
            WalletData::changeTransactionStatus($transactionData->id,'reverse',Auth::user()->modelPath,Auth::id());
            $payment_invoice->update(['status'=>'reverse']);

            return $handleResponse;
        }

        $paymentTransaction->update([
            'response_type'=> 'done',
            'response'=> self::object2array($response)
        ]);

        if(isset($inquiryTransaction)){
            $inquiryTransaction->update(['is_paid'=> 'yes']);
        }

        WalletData::changeTransactionStatus($transactionData->id,'paid',Auth::user()->modelPath,Auth::id());
        $payment_invoice->update(['status'=>'paid']);


        $handleResponse['data']->system_amount = [
            'status'        => true,
            'amount'        => $amountData['amount'],
            'total_amount'  => $amountData['total_amount']
        ];

        return $handleResponse;
    }

    /**
     * @param $serviceID
     * @param $amount
     * @return array
     */
    public static function totalAmount($amount){

        $serviceID = self::$serviceID;

        // Check Service Data
        $getServiceData = self::getServiceData($serviceID,'payment');

        if(!$getServiceData || !$getServiceData->payment_sdk || !$getServiceData->payment_service_provider){
            return self::outPut(false,'Service not available',20014);
        }

        if(!(int) $amount){
            $amount = $getServiceData->api_service_value;
        }


        $calculateTotalAmount = self::calculateTotalAmount($amount,'payment');

        if(!$calculateTotalAmount['status']){
            return self::outPut(false,$calculateTotalAmount['msg'],100,0);
        }

        return self::outPut(true,'Price successfully updated',100,$calculateTotalAmount);

    }

    /**
     * @return bool|\SimpleXMLElement
     */
    public static function balance(){
        return static::makeRequest('GetBalance',[]);
    }

    /**
     * @param int $serviceVersion
     * @return bool|\SimpleXMLElement
     */
    public static function serviceList($serviceVersion = 0,$locale = 'en',$type = 'normal'){
        return static::makeRequest('ServiceList',['serviceVersion'=>$serviceVersion],$locale,$type);
    }

    /**
     * @return array|bool|void
     */
    public static function rebuildDataBase(){
        ignore_user_abort(true);
        set_time_limit(0);

        $serviceListEN = self::object2array(self::serviceList());
        $serviceListAR = self::object2array(self::serviceList(0,'ar'));


        $serviceInputParameterEN = $serviceListEN['data']['serviceInputParameterList']['serviceInputParameter'];
        $serviceInputParameterAR = $serviceListAR['data']['serviceInputParameterList']['serviceInputParameter'];

        $serviceAPIParameters = [];

        foreach ($serviceInputParameterEN as $key => $value) {
            $valueData = $value;
            $valueData['name_ar'] = $serviceInputParameterAR[$key]['name'];

            $serviceAPIParameters[$value['serviceAccountId']][] = $valueData;
        }


        /*
         * Get Category Data
         */
        $categories = [];
        foreach ($serviceListEN['data']['providerGroupList']['providerGroup'] as $key => $value){
            $categories[] = [
                'id'=> $value['id'],
                'name_ar' => $serviceListAR['data']['providerGroupList']['providerGroup'][$key]['name'],
                'name_en' => $value['name'],
                'status'=> 'active'
            ];
        }
        PaymentServiceProviderCategories::insert($categories);

        /*
        * Get Providers Data
        */
        $providers = [];
        foreach ($serviceListEN['data']['providerList']['provider'] as $key => $value){
            $providers[] = [
                'id'=> $value['id'],
                'payment_service_provider_category_id'=> $value['providerGroupId'],
                'name_ar' => $serviceListAR['data']['providerList']['provider'][$key]['name'],
                'name_en' => $value['name'],
                'status'=> 'active'
            ];
        }

        PaymentServiceProviders::insert($providers);


       /*
       * Get Services Data
       */
        $servicesAR = [];
        $servicesEN = [];
        foreach ($serviceListEN['data']['serviceList']['service'] as $key => $value){
            $servicesAR[] = $serviceListAR['data']['serviceList']['service'][$key];
            $servicesEN[] = $value;
        }


        $servicesGroupByEN = collect($servicesEN)->groupBy('providerId');
        $servicesGroupByAR = collect($servicesAR)->groupBy('providerId');

        $accounts = $accountsAR = [];

        // Insert Non Bills Services
        foreach ($servicesGroupByEN as $key => $value) {
            foreach ($value as $key2 => $value2){
                // Exception
                if(strtolower($value2['name']) == 'mc bill inquiry'){
                    $value2['name'] = 'mc electricity inquiry';
                }elseif(strtolower($value2['name']) == 'pay oldest bill'){
                    $value2['name'] = 'payment oldest bill';
                }
                // Exception


                if(strpos($value2['name'],'nquiry')){
                    $type         = 'inquiry';
                    $accounts[]   = $value2;
                    $accountsAR[] = $servicesGroupByAR[$key][$key2];
                }elseif(strpos($value2['name'],'nquire')){
                    $type         = 'inquire';
                    $accounts[]   = $value2;
                    $accountsAR[] = $servicesGroupByAR[$key][$key2];
                }elseif(strpos($value2['name'],'ayment')){
                    $type         = 'payment';
                    $accounts[]   = $value2;
                    $accountsAR[] = $servicesGroupByAR[$key][$key2];
                }else{

                    if(is_array($value2['serviceValue']) && empty($value2['serviceValue'])){
                        $value2['serviceValue'] = '';
                    }

                    if(is_array($value2['priceType'])){
                        $value2['priceType'] = '';
                    }
                    if(is_array($value2['serviceValue'])){
                        $value2['serviceValue'] = '';
                    }
                    if(is_array($value2['serviceValueList'])){
                        $value2['serviceValueList'] = '';
                    }
                    if(is_array($value2['minValue'])){
                        $value2['minValue'] = '';
                    }
                    if(is_array($value2['maxValue'])){
                        $value2['maxValue'] = '';
                    }
                    if(is_array($value2['commissionType'])){
                        $value2['commissionType'] = '';
                    }
                    if(is_array($value2['commissionValueType'])){
                        $value2['commissionValueType'] = '';
                    }
                    if(is_array($value2['fixedCommission'])){
                        $value2['fixedCommission'] = '';
                    }
                    if(is_array($value2['defaultCommission'])){
                        $value2['defaultCommission'] = '';
                    }
                    if(is_array($value2['fromCommission'])){
                        $value2['fromCommission'] = '';
                    }
                    if(is_array($value2['toCommission'])){
                        $value2['toCommission'] = '';
                    }

                    $PaymentServices = PaymentServices::create([
                        'payment_sdk_id'=> '1',
                        'payment_service_provider_id'=> (string) $value2['providerId'],
                        'name_ar'=> (string) $servicesGroupByAR[$key][$key2]['name'],
                        'name_en'=> (string) $value2['name'],
                        'status'=> 'active',
                        'staff_id'=> '1'
                    ]);




                    $PaymentServiceAPIs = PaymentServiceAPIs::create([
                        'payment_service_id'=> $PaymentServices->id,
                        'service_type'=> 'payment',
                        'name'=> (string) $value2['name'],
                        'external_system_id'=> (string) $value2['accountId'],
                        'price_type'=> (string) $value2['priceType'],
                        'service_value'=> (string) $value2['serviceValue'],
                        'service_value_list'=> (string) $value2['serviceValueList'],
                        'min_value'=> (string) $value2['minValue'],
                        'max_value'=> (string) $value2['maxValue'],
                        'commission_type'=> (string) $value2['commissionType'],
                        'commission_value_type'=> (string) $value2['commissionValueType'],
                        'fixed_commission'=> (string) $value2['fixedCommission'],
                        'default_commission'=> (string) $value2['defaultCommission'],
                        'from_commission'=> (string) $value2['fromCommission'],
                        'to_commission'=> (string) $value2['toCommission'],
                        'staff_id'=> 1
                    ]);



                    if(isset($serviceAPIParameters[$value2['accountId']])){
                        foreach ($serviceAPIParameters[$value2['accountId']] as $pKey => $pValue) {
                            if(is_array($pValue['defaultValue']) && empty($pValue['defaultValue'])){
                                $pValue['defaultValue'] = '';
                            }
                            PaymentServiceAPIParameters::create([
                                'external_system_id'=> $pValue['id'],
                                'payment_services_api_id'=> $PaymentServiceAPIs->id,
                                'name_ar'=>  (string)$pValue['name_ar'],
                                'name_en'=> (string) $pValue['name'],
                                'position'=> (string) $pValue['position'],
                                'visible'=> (string) $pValue['visible'],
                                'required'=> (string) $pValue['required'],
                                'type'=> (string) $pValue['type'],
                                'is_client_id'=> (string) $pValue['isClientId'],
                                'default_value'=>  (string)$pValue['defaultValue'],
                                'min_length'=> (string) $pValue['minLength'],
                                'max_length'=>  (string) $pValue['maxLength'],
                                'staff_id'=> 1
                            ]);
                        }
                    }

                }
            }
        }




        // Insert Bill Services
        $accountsCollect = collect($accounts);
        $billsServices = collect($accounts)->groupBy('providerId');
        $billsServicesAR = collect($accountsAR)->groupBy('providerId');
        $failServices = $BillPaymentAccountIDs = [];



        foreach ($billsServices as $key => &$value){

            foreach ($value as $key2 => $value2){
                // Handle Array to String

                if(is_array($value2['priceType'])){
                    $value2['priceType'] = '';
                }
                if(is_array($value2['serviceValue'])){
                    $value2['serviceValue'] = '';
                }
                if(is_array($value2['serviceValueList'])){
                    $value2['serviceValueList'] = '';
                }
                if(is_array($value2['minValue'])){
                    $value2['minValue'] = '';
                }
                if(is_array($value2['maxValue'])){
                    $value2['maxValue'] = '';
                }
                if(is_array($value2['commissionType'])){
                    $value2['commissionType'] = '';
                }
                if(is_array($value2['commissionValueType'])){
                    $value2['commissionValueType'] = '';
                }
                if(is_array($value2['fixedCommission'])){
                    $value2['fixedCommission'] = '';
                }
                if(is_array($value2['defaultCommission'])){
                    $value2['defaultCommission'] = '';
                }
                if(is_array($value2['fromCommission'])){
                    $value2['fromCommission'] = '';
                }
                if(is_array($value2['toCommission'])){
                    $value2['toCommission'] = '';
                }

                // Get Inquiry Then get Payment and add them to one service

                if(strpos($value2['name'],'nquiry') || strpos($value2['name'],'nquire')){

                    $sName = strtolower($value2['name']);

                    if(strpos($value2['name'],'nquiry')){
                        $type = 'inquiry';
                    }else{
                        $type = 'inquire';
                    }

                    $payment = $value->filter(function($item,$key) use($sName,$type,$value2) {
                        return strtolower($item['name']) == strtolower(str_replace($type,'payment',$sName));
                    });



                    if($payment->isEmpty()){
                        $payment = $value->filter(function($item,$key) use($sName,$type) {
                            return strpos(strtolower($item['name']),strtolower(str_replace($type,'payment',$sName))) !== false;
                        });
                    }


                    if($payment->isNotEmpty()){

                        $inquiryArrayData = $value2;

                        foreach ($payment as $keyPayment => $valuePayment){
                            $paymentArrayData = $valuePayment;

                            if(is_array($paymentArrayData['priceType'])){
                                $paymentArrayData['priceType'] = '';
                            }
                            if(is_array($paymentArrayData['serviceValue'])){
                                $paymentArrayData['serviceValue'] = '';
                            }
                            if(is_array($paymentArrayData['serviceValueList'])){
                                $paymentArrayData['serviceValueList'] = '';
                            }
                            if(is_array($paymentArrayData['minValue'])){
                                $paymentArrayData['minValue'] = '';
                            }
                            if(is_array($paymentArrayData['maxValue'])){
                                $paymentArrayData['maxValue'] = '';
                            }
                            if(is_array($paymentArrayData['commissionType'])){
                                $paymentArrayData['commissionType'] = '';
                            }
                            if(is_array($paymentArrayData['commissionValueType'])){
                                $paymentArrayData['commissionValueType'] = '';
                            }
                            if(is_array($paymentArrayData['fixedCommission'])){
                                $paymentArrayData['fixedCommission'] = '';
                            }
                            if(is_array($paymentArrayData['defaultCommission'])){
                                $paymentArrayData['defaultCommission'] = '';
                            }
                            if(is_array($paymentArrayData['fromCommission'])){
                                $paymentArrayData['fromCommission'] = '';
                            }
                            if(is_array($paymentArrayData['toCommission'])){
                                $paymentArrayData['toCommission'] = '';
                            }

                            // Create Inquiry Service
                            $PaymentServices = PaymentServices::create([
                                'payment_sdk_id'=> '1',
                                'payment_service_provider_id'=> (string) $inquiryArrayData['providerId'],
                                'name_ar'=> (string) $billsServicesAR[$key][$key2]['name'],
                                'name_en'=> (string) $inquiryArrayData['name'],
                                'status'=> 'active',
                                'staff_id'=> '1'
                            ]);

                            // Bill Inquiry
                            $billInquiry = PaymentServiceAPIs::create([
                                'payment_service_id'=> $PaymentServices->id,
                                'service_type'=> 'inquiry',
                                'name'=> (string) $inquiryArrayData['name'],
                                'external_system_id'=>(string)  $inquiryArrayData['accountId'],
                                'price_type'=>(string)  $inquiryArrayData['priceType'],
                                'service_value'=>(string)  $inquiryArrayData['serviceValue'],
                                'service_value_list'=>(string)  $inquiryArrayData['serviceValueList'],
                                'min_value'=> (string) $inquiryArrayData['minValue'],
                                'max_value'=>(string)  $inquiryArrayData['maxValue'],
                                'commission_type'=> (string) $inquiryArrayData['commissionType'],
                                'commission_value_type'=> (string) $inquiryArrayData['commissionValueType'],
                                'fixed_commission'=> (string) $inquiryArrayData['fixedCommission'],
                                'default_commission'=> (string) $inquiryArrayData['defaultCommission'],
                                'from_commission'=> (string) $inquiryArrayData['fromCommission'],
                                'to_commission'=> (string) $inquiryArrayData['toCommission'],
                                'staff_id'=> 1
                            ]);

                            if(isset($serviceAPIParameters[$inquiryArrayData['accountId']])){
                                foreach ($serviceAPIParameters[$inquiryArrayData['accountId']] as $pKey => $pValue) {
                                    if(is_array($pValue['defaultValue']) && empty($pValue['defaultValue'])){
                                        $pValue['defaultValue'] = '';
                                    }
                                    PaymentServiceAPIParameters::create([
                                        'external_system_id'=> $pValue['id'],
                                        'payment_services_api_id'=> $billInquiry->id,
                                        'name_ar'=>  (string)$pValue['name_ar'],
                                        'name_en'=>  (string)$pValue['name'],
                                        'position'=> (string) $pValue['position'],
                                        'visible'=> (string) $pValue['visible'],
                                        'required'=>  (string)$pValue['required'],
                                        'type'=> (string) $pValue['type'],
                                        'is_client_id'=> (string) $pValue['isClientId'],
                                        'default_value'=> (string) $pValue['defaultValue'],
                                        'min_length'=> (string) $pValue['minLength'],
                                        'max_length'=> (string) $pValue['maxLength'],
                                        'staff_id'=> 1
                                    ]);
                                }
                            }


                            $BillPaymentAccountIDs[] = $paymentArrayData['accountId'];

                            // Bill Payment
                            $billPayment = PaymentServiceAPIs::create([
                                'payment_service_id'=> $PaymentServices->id,
                                'service_type'=> 'payment',
                                'name'=> (string) $paymentArrayData['name'],
                                'external_system_id'=>(string)  $paymentArrayData['accountId'],
                                'price_type'=> (string) $paymentArrayData['priceType'],
                                'service_value'=> (string) $paymentArrayData['serviceValue'],
                                'service_value_list'=>(string)  $paymentArrayData['serviceValueList'],
                                'min_value'=>(string)  $paymentArrayData['minValue'],
                                'max_value'=> (string) $paymentArrayData['maxValue'],
                                'commission_type'=>(string)  $paymentArrayData['commissionType'],
                                'commission_value_type'=> (string) $paymentArrayData['commissionValueType'],
                                'fixed_commission'=>(string)  $paymentArrayData['fixedCommission'],
                                'default_commission'=> (string) $paymentArrayData['defaultCommission'],
                                'from_commission'=>(string)  $paymentArrayData['fromCommission'],
                                'to_commission'=>(string)  $paymentArrayData['toCommission'],
                                'staff_id'=> 1
                            ]);

                            if(isset($serviceAPIParameters[$paymentArrayData['accountId']])){
                                foreach ($serviceAPIParameters[$paymentArrayData['accountId']] as $pKey => $pValue) {
                                    if(is_array($pValue['defaultValue']) && empty($pValue['defaultValue'])){
                                        $pValue['defaultValue'] = '';
                                    }
                                    PaymentServiceAPIParameters::create([
                                        'external_system_id'=> $pValue['id'],
                                        'payment_services_api_id'=> $billPayment->id,
                                        'name_ar'=> (string) $pValue['name_ar'],
                                        'name_en'=> (string) $pValue['name'],
                                        'position'=> (string) $pValue['position'],
                                        'visible'=> (string) $pValue['visible'],
                                        'required'=> (string) $pValue['required'],
                                        'type'=> (string) $pValue['type'],
                                        'is_client_id'=> (string) $pValue['isClientId'],
                                        'default_value'=> (string) $pValue['defaultValue'],
                                        'min_length'=> (string) $pValue['minLength'],
                                        'max_length'=> (string) $pValue['maxLength'],
                                        'staff_id'=> 1
                                    ]);
                                }
                            }


                        }


                    }else{
                        $failServices[] = $value2;
                    }

                }


            }
        }





        if(empty($failServices)){
            return true;
        }else{
            $newfailServices = [];
            foreach ($failServices as $key => $value){
                if(!in_array($value['accountId'],$BillPaymentAccountIDs)){
                    $newfailServices[] = $value;
                }
            }

            if(empty($newfailServices)){
                return true;
            }else{
                return $newfailServices;
            }
        }


    }

    /**
     * @param int $externalID
     * @return bool|\SimpleXMLElement
     */
    public static function transactionStatus(int $externalID){
        return static::makeRequest('TransactionStatus',['transactionId'=> $externalID]);
    }

    public static function ReviewTransaction(PaymentTransactions $paymentTransactions){
        if(!isset($paymentTransactions->payment_services))
            $paymentTransactions->payment_services()->with('payment_service_provider')->first();

            $paymentTransactions->payment_services->payment_service_provider_id = $paymentTransactions->payment_services->payment_service_provider->id;
            $paymentTransactions->payment_services->payment_service_provider_category_id =  $paymentTransactions->payment_services->payment_service_provider->payment_service_provider_category_id;
            $paymentTransactions->payment_services->payment_service_provider_name_ar = $paymentTransactions->payment_services->payment_service_provider->name_ar;
            $paymentTransactions->payment_services->payment_service_name_ar = $paymentTransactions->payment_services->name_ar;
            $paymentTransactions->payment_services->payment_service_provider_name_en = $paymentTransactions->payment_services->payment_service_provider->name_en;
            $paymentTransactions->payment_services->payment_service_name_en = $paymentTransactions->payment_services->name_en;

            $paymentTransactions->payment_services->payment_service_description_ar = $paymentTransactions->payment_services->description_ar;
            $paymentTransactions->payment_services->payment_service_description_en = $paymentTransactions->payment_services->description_en;

//            $paymentTransactions->payment_services->payment_service_description_en = $paymentTransactions->payment_services->description_en;
//            $paymentTransactions->payment_services->payment_service_description_ar = $paymentTransactions->payment_services->description_ar;


        return self::handleResponse('Transaction',response_to_object($paymentTransactions->response),$paymentTransactions->payment_services);
    }

}