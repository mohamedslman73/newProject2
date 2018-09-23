<?php

namespace App\Libs\Payments;

use App\Models\PaymentSDK;
use App\Models\PaymentServiceAPIParameters;
use App\Models\PaymentServiceAPIs;
use App\Models\PaymentServiceProviderCategories;
use App\Models\PaymentServiceProviders;
use App\Models\PaymentServices;
use Auth;
use Mockery\Exception;

class Payments{

    public static $adapter,$service = false;

    /**
     * START
     * Get Tables Data
     */

    // @TODO: Add where status in functions

    public static function getServiceProviderCategories($select = '*'){
        $Data = PaymentServiceProviderCategories::where('status','active');
        if($select != '*'){
            $Data->select($select);
        }

        return $Data->get();
    }

    public static function getServiceProviders($category = null,$select = '*'){
        $Data = PaymentServiceProviders::join('payment_service_provider_categories','payment_service_provider_categories.id','=','payment_service_providers.payment_service_provider_category_id')
            ->where('payment_service_providers.status','active')
            ->where('payment_service_provider_categories.status','active');

        if($select != '*'){
            $Data->select($select);
        }

        if($category){
            $Data->where('payment_service_providers.payment_service_provider_category_id',$category);
        }

        return $Data->get();
    }

    public static function getPaymentServices($provider = null,$select = '*'){
        $Data = PaymentServices::join('payment_sdk','payment_sdk.id','=','payment_services.payment_sdk_id')
            ->join('payment_service_providers','payment_service_providers.id','=','payment_services.payment_service_provider_id')
            ->where('payment_services.status','active')
            ->where('payment_service_providers.status','active');

        if($select != '*'){
            $Data->select($select);
        }

        if($provider){
            $Data->where('payment_services.payment_service_provider_id',$provider);
        }

        return $Data->get();
    }

    public static function getPaymentServiceAPIs($service = null,$select = '*'){
        $Data = PaymentServiceAPIs::join('payment_services','payment_services.id','=','payment_service_apis.payment_service_id')
            ->where('payment_services.status','active');

        if($select != '*'){
            $Data->select($select);
        }

        if($service){
            $Data->where('payment_service_apis.payment_service_id',$service);
        }

        return $Data->get();

    }

    public static function getPaymentServiceAPIParameters($serviceAPI = null,$select = '*'){
        $Data = PaymentServiceAPIParameters::join('payment_service_apis','payment_service_apis.id','=','payment_service_api_parameters.payment_services_api_id');

        if($select != '*'){
            $Data->select($select);
        }

        if($serviceAPI){
            $Data->where('payment_service_apis.payment_service_id',$serviceAPI);
        }

        return $Data->get();

    }

    /**
     * END
     * Get Tables Data
     */

    public static function selectAdapterByService($serviceID){

        if(!Auth::id()){
            throw new Exception('Auth Failed');
        }

        $serviceData = PaymentServices::find($serviceID);
        if(!$serviceData || !$serviceData->payment_sdk || !$serviceData->payment_service_provider){
            return false;
        }

        $adapter = $serviceData->payment_sdk->adapter_name;

        $adapterObject = '\App\Libs\Payments\Adapters\\'.$adapter;

        if( class_exists($adapterObject) && new $adapterObject instanceof \App\Libs\Payments\PaymentInterface){
            self::$adapter = new $adapterObject;
            self::$adapter::$serviceID = $serviceID;
            return self::$adapter;
        }else{
            return false;
        }

    }

    /*
     * DEPRECATED
     */
    public static function selectAdapterByID($ID){
        if(!Auth::id()){
            throw new Exception('Auth Failed');
        }

        $adapter = PaymentSDK::find($ID);
        if(!$adapter){
            return false;
        }

        $adapterObject = '\App\Libs\Payments\Adapters\\'.$adapter->adapter_name;
        if( class_exists($adapterObject) && new $adapterObject instanceof \App\Libs\Payments\PaymentInterface){
            self::$adapter = new $adapterObject;
            return self::$adapter;
        }else{
            return false;
        }

    }

}