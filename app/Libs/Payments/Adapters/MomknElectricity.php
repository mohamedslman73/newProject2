<?php

namespace App\Libs\Payments\Adapters;

use App\Libs\WalletData;
use App\Models\Merchant;
use App\Models\MerchantStaff;
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


/**
 * Trait BeeAdditionalFunction
 * @package App\Libs\Payments\Adapters
 */
trait MomknElectricityAdditionalFunction
{
    /**
     * @var
     */
    private static $getServiceData;

    /**
     * @param $object
     * @return mixed
     */
    private static function object2array($object)
    {
        return @json_decode(@json_encode($object), 1);
    }

    /**
     * @param $object
     * @return mixed
     */
    private static function array2object($object)
    {
        return @json_decode(@json_encode($object));
    }

    /**
     * @param $serviceID
     * @param $serviceType
     * @param bool $updateMainValue
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    private static function getServiceData($serviceID, $serviceType, $updateMainValue = true)
    {

        $getServiceData = PaymentServices::join('payment_service_apis', 'payment_service_apis.payment_service_id', '=', 'payment_services.id')
            ->join('payment_service_providers', 'payment_service_providers.id', '=', 'payment_services.payment_service_provider_id')
            ->where('payment_services.status', 'active')
            ->where('payment_service_providers.status', 'active')
            ->where('payment_service_apis.service_type', '=', $serviceType)
            ->where('payment_services.id', '=', $serviceID)
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

        if ($updateMainValue) {
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
    private static function outPut($status, $msg, $code, $data = [])
    {
        return [
            'status' => $status,
            'msg' => (empty($msg)) ? '' : __((string)$msg),
            'code' => $code,
            'data' => (object)$data
        ];
    }

    /**
     * @param $APIID
     * @param $requestMap
     * @return array|bool
     */
    private static function validator($APIID, $requestMap)
    {
        // Validate Parameters
        $parametersFromTable = PaymentServiceAPIParameters::where('payment_services_api_id', $APIID)->get();
        if (!$parametersFromTable) {
            $parametersFromTable = [];
        } else {
            $parametersFromTable = $parametersFromTable->toArray();
        }

        $parameters = Validator::service($parametersFromTable, $requestMap);
        if ($parameters !== true) {
            return $parameters;
        } else {
            return true;
        }

        // Validate Parameters
    }

    /**
     * @param $clientAmount
     * @param $serviceType
     * @return array
     */
    private static function calculateTotalAmount($amount, $totalAmount)
    {
        return [
            'status' => true,
            'amount' => $amount,
            'total_amount' => $totalAmount
        ];

    }

    public static function totalAmount($amount)
    {

        $serviceID = self::$serviceID;

        // Check Service Data
        $getServiceData = self::getServiceData($serviceID, 'payment');

        if (!$getServiceData || !$getServiceData->payment_sdk || !$getServiceData->payment_service_provider) {
            return self::outPut(false, 'Service not available', 20014);
        }

        if (!(int)$amount) {
            $amount = $getServiceData->api_service_value;
        }


        $calculateTotalAmount = self::calculateTotalAmount($amount, 'payment');

        if (!$calculateTotalAmount['status']) {
            return self::outPut(false, $calculateTotalAmount['msg'], 100, 0);
        }

        return self::outPut(true, 'Price successfully updated', 100, $calculateTotalAmount);

    }


    /**
     * @param $serviceID
     * @param $amount
     * @param $totalAmount
     * @param $APIExternalSystemID
     * @param $serviceType
     * @return mixed
     */
    private static function createPaymentTransaction($serviceID, $amount, $totalAmount, $APIExternalSystemID, $serviceType)
    {
        return Auth::user()->PaymentTransactions()->create([
            'payment_services_id' => $serviceID,
            'amount' => $amount,
            'total_amount' => $totalAmount,
            'request_map' => Validator::$parametersToSDK,
            'external_system_id' => $APIExternalSystemID,
            'service_type' => $serviceType
        ]);
    }

    // Handle Data

    /**
     * @param $data
     * @return int
     */
    private static function handleAmount($data)
    {

        if (isset($data['information'])) {
            return $data['information'];
        } elseif (isset($data['due_amount'])) {
            return $data['due_amount'];
        } elseif (isset($data['amount'])) {
            return $data['amount'];
        } elseif (isset($data['default_amount'])) {
            return $data['default_amount'];
        } elseif (isset($data['invoice_total_due_amount'])) {
            return $data['invoice_total_due_amount'];
        } elseif (isset($data['balance'])) {
            return $data['balance'];
        }

        return 0;
    }

    /**
     * @param $data
     * @return int
     */
    private static function handleMinValue($data)
    {
        if (isset($data['min_value'])) {
            return $data['min_value'];
        } elseif (isset($data['min'])) {
            return $data['min'];
        } elseif (isset($data['range_min_amount'])) {
            return $data['range_min_amount'];
        } elseif (isset($data['min_amount'])) {
            return $data['min_amount'];
        }

        return 0;
    }

    /**
     * @param $data
     * @return int
     */
    private static function handleMaxValue($data)
    {
        if (isset($data['max_value'])) {
            return $data['max_value'];
        } elseif (isset($data['max'])) {
            return $data['max'];
        } elseif (isset($data['range_max_amount'])) {
            return $data['range_max_amount'];
        } elseif (isset($data['max_amount'])) {
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
    private static function handleResponse($action, $response, $serviceData, $paymentTransaction = null)
    {
        $response = (array) $response;
        if (!$response || !$response['Message']) {
            return self::outPut(false, 'Service not available', 20001);
        }

        $return = [];
        // Handle Response Status

        if($response['Code'] != '200'){
            return self::outPut(false, $response['Message'], 20001);
        }

        // Start Handel Data
        switch ($action) {
            case 'inquiry':
            case 'payment':

                if($paymentTransaction){
                    $return['transactionId'] = $paymentTransaction->id;
                }else{
                    $return['transactionId'] = 0;
                }

                $return['dateTime'] = date('Y-m-d H:i:s');

                // ----- INFO DATA
                $return['info'] = [];
                $return['info']['payment_output_id'] = $serviceData->payment_output->id;

                $outPutParameters = array_column($serviceData->payment_output->parameters, 'language', 'key');


                // H INFO
                $return['Hinfo']['Account_number'] = $response['Account_number'];
                $return['Hinfo']['AccountName'] = $response['AccountName'];
                $return['Hinfo']['Address'] = $response['Address'];
                $return['Hinfo']['Due_Date'] = $response['Due_Date'];
                $return['Hinfo']['Bill_Number'] = $response['Bill_Number'];
                $return['Hinfo']['Bills_Count'] = $response['Bills_Count'];
                $return['Hinfo']['EPay_Bill_Record_ID'] = $response['EPay_Bill_Record_ID'];
                $return['Hinfo']['Bill_Number_val'] = $response['Bill_Number_val'];
                $return['Hinfo']['Default_Amount'] = $response['Default_Amount'];
                $return['Hinfo']['EFinance_Fees'] = $response['EFinance_Fees'];
                $return['Hinfo']['Request_id_inquiry'] = $response['Request_id_inquiry'];
                $return['Hinfo']['Ref_Info'] = $response['Ref_Info'];
                $return['Hinfo']['Code'] = $response['Code'];
                $return['Hinfo']['Message'] = $response['Message'];
                $return['Hinfo']['Amount'] = $response['Amount'];
                $return['Hinfo']['AddedMoney'] = $response['AddedMoney'];
                $return['Hinfo']['Total_Amount'] = $response['Total_Amount'];
                $return['Hinfo']['Request_id_payment'] = $response['Request_id_payment'];
                $return['Hinfo']['Invoice_num'] = $response['Invoice_num'];
                $return['Hinfo']['UserId'] = $response['UserId'];
                $return['Hinfo']['totalWithAddedMoney'] = $response['totalWithAddedMoney'];
                $return['Hinfo']['billingAccount'] = $response['billingAccount'];

                // H INFO

                $return['info']['amount']       = 1;//$response['Amount'];
                $return['info']['total_amount'] = 1.1;//$response['totalWithAddedMoney'];
                $return['info']['min_value']    = $response['totalWithAddedMoney'];
                $return['info']['max_value']    = $response['totalWithAddedMoney'];
                $return['info']['invoices']     = $response['Invoices'];


                $return['info']['ar'] = [
                    [
                        'key'=> 'اسم العميل',
                        'value'=> $response['AccountName']
                    ],
                    [
                        'key'=> 'عدد الفواتير',
                        'value'=> $response['Bills_Count']
                    ],
                    [
                        'key'=> 'شهر',
                        'value'=> $response['Ref_Info']
                    ]
                ];

                $return['info']['en'] = [
                    [
                        'key'=> 'Account Name',
                        'value'=> $response['AccountName']
                    ],
                    [
                        'key'=> 'Bills Count',
                        'value'=> $response['Bills_Count']
                    ],
                    [
                        'key'=> 'Month',
                        'value'=> $response['Ref_Info']
                    ]
                ];


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
                return self::outPut(false, 'Service not available', 20005);
                break;
        }


        $return['service_info'] = [
            // @TODO : set real merchant ID or staff id
            // 'merchant_id'=> Auth()->user()->merchant()->id,
            'service_id' => $serviceData->id,
            'provider_id' => $serviceData->payment_service_provider_id,
            'provider_name_ar' => $serviceData->payment_service_provider_name_ar,
            'service_name_ar' => $serviceData->payment_service_name_ar,
            'provider_name_en' => $serviceData->payment_service_provider_name_en,
            'service_name_en' => $serviceData->payment_service_name_en,
            'service_description_en' => $serviceData->payment_service_description_en,
            'service_description_ar' => $serviceData->payment_service_description_ar
        ];


        $return['payment_by'] = [
            'name' => 'Bee',
            'logo' => 'Bee'
        ];

        return self::outPut(true, 'Successful', 20006, $return);

    }

    /**
     * @param $action
     * @param array $data
     * @param $locale
     * @return mixed
     */
    private static function generateXMLRequest($action, array $data, $locale)
    {

        if (isset($data['serviceData'])) {
            unset($data['serviceData']);
        }

        if ($action == 'Transaction') {
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Request action="' . $action . '" version="2"/>');
        } else {
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Request action="' . $action . '" version="1"/>');
        }

        $xml->addChild('login', "6659188541");
        $xml->addChild('password', "qazxsw");
        $xml->addChild('locale', $locale);
        $xml->addChild('terminal_id', 1);

        if (!empty($data)) {
            $xmlData = $xml->addChild('data');
            foreach ($data as $key => $value) {
                if ($key == 'requestMap') continue;
                $xmlData->addChild($key, $value);
            }


            if ($action == 'Transaction') {

                $requestMap = $xmlData->addChild('requestMap');
                if (isset($data['requestMap']) && !empty($data['requestMap'])) {
                    foreach ($data['requestMap'] as $key => $value) {
                        $item = $requestMap->addChild('item');
                        $item->addChild('key', substr($key, 10));
                        $item->addChild('value', $value);
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
class MomknElectricity implements \App\Libs\Payments\PaymentInterface
{

    use MomknElectricityAdditionalFunction;

    /**
     * @var int
     */

    public static $serviceID = null;

    /**
     * @param $action
     * @param array $data
     * @param string $locale
     * @return bool|\SimpleXMLElement
     */
    private static function makeRequest($action, array $data, $locale = 'en')
    {

//        dd($data);
        $params = [];
        $params['UserName'] = 'B577';
        $params['Password'] = '163974';
        $params['CenterID'] = '24813';
        $params['service_id']  = $data['serviceAccountId'];

        if($action == 'inquiry'){

            $params['account_number']   = $data['requestMap']['parameter_990000001'];

            $implodeData = [];

            foreach ($params as $key => $value){
                $implodeData[] = $key.'='.$value;
            }

            $MomknURL = 'http://m-api.cf/MomknServices/api/Electricity_Efinance?'.implode('&',$implodeData);

            // Rrequests Hooks
            $hooks = new Requests_Hooks();
            $hooks->register('curl.before_request', function ($handle) {
                curl_setopt($handle, CURLOPT_FAILONERROR, true);
                curl_setopt($handle, CURLOPT_SSLVERSION, 3);
                curl_setopt($handle, CURLOPT_VERBOSE, true);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_HEADER, false);
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            });

            try {
                $response = Requests::get($MomknURL, [], ['hooks' => $hooks, 'timeout' => 1000, 'connect_timeout' => 1000]);
                return \GuzzleHttp\json_decode($response->body);
            } catch (\Exception $exception) {
                return false;
            }

        }elseif($action == 'payment'){
            $MomknURL = 'http://www.momkn.org:9090/MomknServices/api/ElectricityPaymentlast';

            $params['account_number'] = $data['requestMap']['parameter_990000001'];

            $params['Amount_val'] = $data['inquiryResponse']['data']->info['total_amount'];
            $params['AccountName'] = $data['inquiryResponse']['data']->Hinfo['AccountName'];
            $params['Address'] = $data['inquiryResponse']['data']->Hinfo['Address'];

            $params['Due_Date'] = $data['inquiryResponse']['data']->Hinfo['Due_Date'];
            $params['Bills_Count'] = $data['inquiryResponse']['data']->Hinfo['Bills_Count'];
            $params['ePay_Bill_Record_ID'] = $data['inquiryResponse']['data']->Hinfo['EPay_Bill_Record_ID'];
            $params['Bill_Number_val'] = $data['inquiryResponse']['data']->Hinfo['Bill_Number_val'];
            $params['Default_Amount'] = $data['inquiryResponse']['data']->Hinfo['Default_Amount'];

            $params['EFinance_Fees'] = $data['inquiryResponse']['data']->Hinfo['EFinance_Fees'];
            $params['request_id_inquiry'] = $data['inquiryResponse']['data']->Hinfo['Request_id_inquiry'];
            $params['paymentRefInfo'] = $data['inquiryResponse']['data']->Hinfo['Ref_Info'];
            $params['billingAccount'] = $data['inquiryResponse']['data']->Hinfo['billingAccount'];



            if(strpos($data['requestMap']['parameter_990000002'],'010') === 0){
                $params['network_id'] = '2';
            }elseif(strpos($data['requestMap']['parameter_990000002'],'012') === 0){
                $params['network_id'] = '1';
            }elseif(strpos($data['requestMap']['parameter_990000002'],'011') === 0){
                $params['network_id'] = '3';
            }else{
                $params['network_id'] = '4';
            }


            $params['mobile_no']  = $data['requestMap']['parameter_990000002'];


            // Rrequests Hooks
            $hooks = new Requests_Hooks();
            $hooks->register('curl.before_request', function ($handle) {
                curl_setopt($handle, CURLOPT_FAILONERROR, true);
                curl_setopt($handle, CURLOPT_SSLVERSION, 3);
                curl_setopt($handle, CURLOPT_VERBOSE, true);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_HEADER, false);
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            });

            try {
                $response = Requests::post($MomknURL, [],$params, ['hooks' => $hooks, 'timeout' => 1000, 'connect_timeout' => 1000]);
                return \GuzzleHttp\json_decode($response->body);
            } catch (\Exception $exception) {
                return false;
            }

        }

    }

    /**
     * @param $serviceID
     * @param array $requestMap
     * @return array
     */
    public static function inquiry($requestMap = [])
    {

        $serviceID = self::$serviceID;

        // Check Service Data
        $getServiceData = self::getServiceData($serviceID, 'inquiry');

        if (!$getServiceData || !$getServiceData->payment_sdk || !$getServiceData->payment_service_provider) {
            return self::outPut(false, 'Service not available', 401);
        }

        // Validate Request Map
        $validator = self::validator($getServiceData->api_id, $requestMap);
        if ($validator !== true) {
            return self::outPut(false, 'Verification Error', 103, $validator);
        }

        //  CreatePayment Transaction
        $paymentTransaction = self::createPaymentTransaction($getServiceData->id, 0, 0, $getServiceData->api_external_system_id, 'inquiry');

        if (!$paymentTransaction) {
            return self::outPut(false, 'Service not available', 402);
        }

        // Make Inquiry using Bee server
        $response = static::makeRequest(
            'inquiry',
            [
                'transactionId' => $paymentTransaction->id, // From Our System
                'serviceAccountId' => $getServiceData->api_external_system_id,
                'amount' => 0,
                'totalAmount' => 0,
                'requestMap' => $paymentTransaction->request_map,
            ]
        );


        $handleResponse = self::handleResponse('inquiry', $response, $getServiceData, $paymentTransaction);


        if (!$handleResponse['status']) {
            $paymentTransaction->update([
                'response_type' => 'fail',
                'response' => self::object2array($response)
            ]);
            return self::outPut(false, $handleResponse['msg'], $handleResponse['code']);
        }

        $handleResponse['data']->system_amount = self::calculateTotalAmount($handleResponse['data']->info['amount'], $handleResponse['data']->info['total_amount']);

        $paymentTransaction->update([
            'amount' => $handleResponse['data']->system_amount['amount'],
            'total_amount' => $handleResponse['data']->system_amount['total_amount'],
            'response_type' => 'done',
            'response' => self::object2array($response)
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


    public static function handleParameters(int $paymentAPIID, array $inquiryRequestMapData, array $paymentRequestMapData)
    {

        $inquiryRequestMapIDs = str_replace('parameter_', '', array_keys($inquiryRequestMapData));
        if (!$inquiryRequestMapIDs) {
            return $paymentRequestMapData;
        }

        // Get external_system_id
        $inquiryParametersData = PaymentServiceAPIParameters::whereIn('external_system_id', $inquiryRequestMapIDs)
            ->get([
                'external_system_id',
                'name_en'
            ]);

        if ($inquiryParametersData->isEmpty()) {
            return $paymentRequestMapData;
        }

        $allPaymentParametersData = PaymentServiceAPIParameters::where('payment_services_api_id', $paymentAPIID)->get([
            'external_system_id',
            'name_en'
        ]);

        foreach ($allPaymentParametersData as $key => $value) {
            foreach ($inquiryParametersData as $key2 => $value2) {
                if (trim(strtolower($value2->name_en)) == trim(strtolower($value->name_en))) {
                    $paymentRequestMapData['parameter_' . $value->external_system_id] = $inquiryRequestMapData['parameter_' . $value2->external_system_id];
                }
            }
        }

        return $paymentRequestMapData;
    }


    public static function payment($requestMap = [], $inquiryTransactionsID = false, $amount = false)
    {
        $serviceID = self::$serviceID;

        if (!$inquiryTransactionsID && !$amount) {
            return self::outPut(false, 'Service not available', 20007);
        }

        // Check Service Data
        $getServiceData = self::getServiceData($serviceID, 'payment');
        if (!$getServiceData || !$getServiceData->payment_sdk || !$getServiceData->payment_service_provider) {
            return self::outPut(false, 'Service not available2', 20008);
        }

        if ($getServiceData->request_amount_input == 'yes') {
            if ($amount == '0' || $amount == '0.0')
                return self::outPut(false, 'Amount can not be empty or 0', 20009);
        }

        $getServiceDataForInquiry = self::getServiceData($getServiceData->id, 'inquiry', false);
        if ($getServiceDataForInquiry && !$inquiryTransactionsID) {
            return self::outPut(false, 'Unknown Error', 20010);
        }

        // Get Amount From Inquiry
        if ($inquiryTransactionsID) {

            $inquiryTransaction = PaymentTransactions::where('id', $inquiryTransactionsID)
                ->where('response_type', 'done')
                ->where('is_paid', 'no')
                ->whereNotNull('response')
                ->first();

            if (!$inquiryTransaction || empty($inquiryTransaction->response)) {
                return self::outPut(false, 'Unknown Error', 20011);
            }

            $inquiryResponse = self::handleResponse('inquiry', self::array2object($inquiryTransaction->response), $getServiceDataForInquiry);

            $amount = $inquiryResponse['data']->info['amount'];

            $requestMap = self::handleParameters($getServiceData->api_id, $inquiryTransaction->request_map, $requestMap);

        }

        $amountData = self::calculateTotalAmount($amount, $inquiryResponse['data']->info['total_amount']);


        if (!$amountData['status']) {
            return self::outPut(false, 'Amount Error', 103, $amountData);
        }

        // Payment Wallet Object Update: 2017-12-07
        $paymentWalletObject = WalletData::getWalletByUserData(Auth::user()->modelPath,Auth::id(),'payment');

        $getWalletBalance = WalletData::balance($paymentWalletObject);
        if ($amountData['total_amount'] > $getWalletBalance) {
            return self::outPut(false, 'You do not have enough credit to make this transaction', 103);
        }

        // Validate Request Map
        $validator = self::validator($getServiceData->api_id, $requestMap);
        if ($validator !== true) {
            return self::outPut(false, 'Verification Error', 103, $validator);
        }

        // Create Payment Transaction
        $paymentTransaction = new \stdClass;
        $payment_invoice = new \stdClass;
        $transactionData = new \stdClass;

        try {
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

            if (!$transactionData || (is_array($transactionData) && !$transactionData['status'])) {
                throw new \Exception('Transaction Error');
            }

        } catch (\PDOException $e) {
            DB::rollBack();
            return self::outPut(false, 'Service not available3', 20012);
        }


        try {

            // Make Inquiry using Bee server
            $response = static::makeRequest(
                'payment',
                [
                    'serviceVersion' => setting('payment_bee_service_version'),
                    'transactionId' => $paymentTransaction->id, // From Our System
                    'serviceAccountId' => $getServiceData->api_external_system_id,
                    'amount' => $amountData['amount'],
                    'totalAmount' => $amountData['total_amount'],
                    'requestMap' => $paymentTransaction->request_map,
                    'inquiryResponse'=> $inquiryResponse
                ]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return self::outPut(false, 'Service not available4', 20013);
        }

        DB::commit();

        $handleResponse = self::handleResponse('Transaction', $response, $getServiceData);

        // If Provider is Down
        if ($response === false) {
            $paymentTransaction->update([
                'response_type' => 'fail',
                'response' => self::object2array($response)
            ]);
            return $handleResponse;
        }
        // If Provider is Down


        if (!$handleResponse['status']) {
            $paymentTransaction->update([
                'response_type' => 'fail',
                'response' => self::object2array($response)
            ]);
            WalletData::changeTransactionStatus($transactionData->id, 'reverse', Auth::user()->modelPath, Auth::id());
            $payment_invoice->update(['status' => 'reverse']);

            return $handleResponse;
        }

        $paymentTransaction->update([
            'response_type' => 'done',
            'response' => self::object2array($response)
        ]);

        if (isset($inquiryTransaction)) {
            $inquiryTransaction->update(['is_paid' => 'yes']);
        }

        WalletData::changeTransactionStatus($transactionData->id, 'paid', Auth::user()->modelPath, Auth::id());
        $payment_invoice->update(['status' => 'paid']);


        $handleResponse['data']->system_amount = [
            'status' => true,
            'amount' => $amountData['amount'],
            'total_amount' => $amountData['total_amount']
        ];

        return $handleResponse;
    }

    /**
     * @return bool|\SimpleXMLElement
     */


    public static function balance()
    {
        return static::makeRequest('GetBalance', []);
    }

    /**
     * @param int $serviceVersion
     * @return bool|\SimpleXMLElement
     */

}