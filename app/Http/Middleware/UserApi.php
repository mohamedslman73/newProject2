<?php

namespace App\Http\Middleware;

use App\Modules\Api\User\UserApiController;
use Closure;
use Auth;
class UserApi
{
    public function handle($request, Closure $next) {
        if(Auth::user()){
            //user not verified
            if ((Auth::user()->verified_at == null) && $request->route()->getName() != 'user.verification'){
                return response()->json([
                    'status' => false,
                    'msg' => __('Account not verified yet'),
                    'code' => 308,
                    'data'=>false
                ],200);
            }
        }
        return $next($request);
    }

}