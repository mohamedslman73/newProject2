<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use App\Models\MerchantStaff;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        //dd(Config::get('session.cookie'));
        if($guard == 'staff') {

            if (Auth::guard($guard)->check()) {
               // dd(Auth::guard($guard)->check());
                return redirect('/system');
            }
        }

        if($guard == 'merchant_staff') {
            if (Auth::guard($guard)->check()) {
                return redirect('/merchant');
            }
        }

        return $next($request);
    }
}
