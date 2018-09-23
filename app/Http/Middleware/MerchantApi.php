<?php

namespace App\Http\Middleware;

use App\Modules\Api\User\MerchantsApiController;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Response;
use Auth;
use Lcobucci\JWT\Parser;

class MerchantApi
{
    public function handle($request, Closure $next)
    {
        if(
            (Auth('apiMerchant')->check()) &&
            (Auth('apiMerchant')->user()->merchant()->status !== 'active' || Auth('apiMerchant')->user()->status !== 'active')
        ) {
            return response()->json([
                'status' => false,
                'msg' => __('Your merchant is disabled'),
                'code' => 100,
                'data'=>false
            ],200);
        }
        $role = str_replace('api','panel',request()->route()->getName());
        if(in_array($role,BypassMerchantPermissions()))
            return $next($request);

        if($role && (!merchantcan($role))){
            return response()->json([
                'status' => false,
                'msg' => __('You don\'t have permission to preform this action'),
                'code' => 100,
                'data'=>false
            ],200);
        }
        if($request->user()) {
            $id = (new Parser())->parse($request->bearerToken())->getHeader('jti');
            $token = $request->user()->tokens()->where('id', '=', $id)->first();
            $token->update(['expires_at' => Carbon::now()->addMinutes(5)]);
        }
        return $next($request);
    }
}