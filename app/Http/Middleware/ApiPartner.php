<?php

namespace App\Http\Middleware;
use App\Models\MerchantStaff;
use Illuminate\Support\Facades\App;
use Closure;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class ApiPartner
{
    public function handle(Request $request, Closure $next)
    {
        App::setLocale('ar');
        if ($request->api_token) {
            $RequestData = $request->only(['api_token']);
            $validator = Validator::make($RequestData, [
                'api_token' => 'required|exists:merchant_staff,api_token',
            ]);
            if ($validator->errors()->any()) {
                return $this->ValidationError($validator, __('Validation Error'));
            }
            return $next($request);
        } else {
            return response()->json([
                'status' => false,
                'msg' => __('Unauthenticated'),
                'code' => 100,
                'data' => false
            ], 200);
        }
    }




}