<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MerchantApiProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
  /*
        app()->bind('auth.passwords', function() {
            dd('here');
        });
*/
        app()->bind('auth.password.broker', function(){
            dd('Test');
        });
    }
}
