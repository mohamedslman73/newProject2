<?php

namespace App\Libs\Payments;

use Validator as basicValidator;

class Validator{

    public static $parametersToSDK = [];

    /**
     * @param array $parameters
     * @param $requestData -> key start with parameter_
     * @return array|bool
     */
    public static function service(array $parameters, $requestData){

        if(!$requestData){
            $requestData = [];
        }

        $collection = collect($parameters);

        $visible = $collection->filter(function($value,$key){
            return $value['visible'] == 'yes';
        })->all();

        $unVisible = $collection->filter(function($value,$key){
            return $value['visible'] == 'no';
        })->all();

        $parameters = $visible;
        if(!empty($parameters)){
            $Rules = [];
            $confirmParameters = [];
            foreach ($parameters as $key => $value){
                if(!is_null($value['confirm_external_id'])){
                    $confirmParameters[] = $value;
                    continue;
                }

                $parameterRules = [];

                // Is Required
                if($value['required'] == 'yes'){
                    $parameterRules[] = 'required';
                }

                // Is Required
                if($value['type'] == 'N'){
                    $parameterRules[] = 'numeric';
                }/*elseif($value['type'] == 'C'){
                    $parameterRules[] = 'regex:/^[a-zA-Z]+$/u';
                }*/

                $parameterRules[] = 'digits_between:'.$value['min_length'].','.$value['max_length'];

                $Rules['parameter_'.$value['external_system_id']] = implode('|',$parameterRules);
            }


            if(!empty($confirmParameters)){
                foreach ($confirmParameters as $key => $value){
                    if(
                    isset($Rules['parameter_'.$value['confirm_external_id']])
                    ){
                        $Rules['parameter_'.$value['confirm_external_id']] .= '|confirmed';
                        if(isset($requestData['parameter_'.$value['external_system_id']])){
                            $requestData['parameter_'.$value['confirm_external_id'].'_confirmation'] = $requestData['parameter_'.$value['external_system_id']];
                        }
                    }
                    unset($requestData['parameter_'.$value['external_system_id']]);
                }
            }

            $validation = basicValidator::make($requestData,$Rules);
            if($validation->fails()){
                return $validation->errors();
            }else{
                $visibleParametersIDs = array_column($visible,'external_system_id');
                $parametersByUser = collect($requestData)->filter(function($value,$key) use($visibleParametersIDs) {
                    if(preg_match('#\_confirmation$#',$key)) return false;
                    return in_array(substr($key,10),$visibleParametersIDs);
                })->all();

                $unVisible = array_column($unVisible,'default_value','external_system_id');

                self::$parametersToSDK = array_merge($parametersByUser,
                    array_combine(
                        array_map(function($k){ return 'parameter_'.$k; }, array_keys($unVisible)),$unVisible
                    )
                );

                return true;
            }

        }else{
            return true;
        }

    }

    public static function input($serviceAccountId,$clientAmount,$requestMap){
        $validation = basicValidator::make([
            'serviceAccountId'=> $serviceAccountId,
            'clientAmount'=> $clientAmount,
            'requestMap'=> $requestMap,
        ],[
            'serviceAccountId'=> 'required|exists:payment_services,id',
            'clientAmount'=> 'required|numeric',
            'requestMap'=> 'is_array'
        ]);

        if($validation->fails()){
            return [
                'status'=> false,
                'msg'=> __('Verification Error'),
                'code'=> 103,
                'data'=> (object)[]//(object) $validation->errors()
            ];
        }else{
            return true;
        }
    }

}