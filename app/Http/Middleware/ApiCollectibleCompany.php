<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\App;
use Closure;
use Auth;

class ApiCollectibleCompany
{
    public function handle($request, Closure $next)
    {

        App::setLocale('ar');

        return $next($request);
    }
}