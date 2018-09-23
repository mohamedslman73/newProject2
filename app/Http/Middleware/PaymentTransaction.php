<?php

namespace App\Http\Middleware;

use App\Models\PaymentServices;
use Closure;
use App\Libs\Payments\Payments;
class PaymentTransaction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if(!is_array($request->requestMap)){
            $request->requestMap = [];
        }

        $payments = Payments::selectAdapterByService($request->serviceID);
        if(!$payments['status']){
            return response($payments, 403);
        }

        $inputValidate = Payments::$adapter::inputValidation($request->serviceID,$request->clientAmount,$request->requestMap);
        if($inputValidate !== true){
            return response($inputValidate, 403);
        }

        $Validate = Payments::$adapter::transactionMiddleware(PaymentServices::find($request->serviceID)->payment_service_apis()->where('service_type',$request->serviceType)->first()->external_system_id,$request->clientAmount,$request->requestMap);
        if($Validate['error']){
            return response($Validate, 403);
        }else{
            $Validate['payment_transaction']->requestMap = $request->requestMap;
            $request->PaymentTransaction = $Validate['payment_transaction'];
        }

        return $next($request);
    }
}