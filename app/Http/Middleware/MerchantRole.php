<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\App;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class MerchantRole
{
    public function handle($request, Closure $next, $role)
    {
        if($request->lang){
            \App::setLocale($request->lang);

        }

        if(!$role)
            $role = request()->route()->getName();
        if(
            (Auth('merchant_staff')->check()) &&
            (Auth('merchant_staff')->user()->merchant()->status !== 'active' || Auth('merchant_staff')->user()->status !== 'active')
        ) {
            Auth('merchant_staff')->logout();
            return redirect(route('merchant.login'),302)
                ->with('msg',__('Your merchant is disabled'));
        }

        /*
         * User Must change password
         */
        if((Auth('merchant_staff')->user()->must_change_password == 1) && !in_array($role,['panel.merchant.user.change-password','panel.merchant.user.update-password'])){
            return redirect()->route('panel.merchant.user.change-password')->with(['msg'=>__('You must change your password')]);
        }
        
        if(!in_array($request->user()->merchant_staff_group_id,$request->user()->merchant()->merchant_staff_group()->pluck('id')->toArray())){
            if (!$request->user()->merchant_staff_permission()->where('route_name',$role)->first()) {
                return redirect(route('merchant.access.denied'),302)
                    ->with('msg',__('You don\'t have permission to access this page'));
            }
        }

        if(!merchantcan($role)){
            return redirect(route('merchant.access.denied'),302)
                ->with('msg',__('You don\'t have permission to access this page'));
        }

        if(in_array($role,['panel.merchant.sub-merchant.index','panel.merchant.sub-merchant.create','panel.merchant.sub-merchant.store','panel.merchant.sub-merchant.edit','panel.merchant.sub-merchant.update'])){
            if($request->user()->merchant()->is_reseller != 'active') {
                return redirect(route('merchant.access.denied'),302)
                    ->with('msg',__('You don\'t have permission to access this page'));
            }
        }

        return $next($request);
    }

}