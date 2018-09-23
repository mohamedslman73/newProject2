<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        /*
        * Fix migrate Error
        */
        Schema::defaultStringLength(191);

       // \App\Models\Merchant::observe(\App\Observers\MerchantObserver::class);
       // \App\Models\MerchantContract::observe(\App\Observers\MerchantContractObserver::class);
       // \App\Models\MerchantProduct::observe(\App\Observers\MerchantProductObserver::class);


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
