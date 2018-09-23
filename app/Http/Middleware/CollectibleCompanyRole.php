<?php

namespace App\Http\Middleware;

 
use Closure;
 
class CollectibleCompanyRole
{
    public function handle($request, Closure $next) {



        App::setLocale('ar');
        return $next($request);
    }

}