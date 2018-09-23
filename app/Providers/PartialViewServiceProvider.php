<?php

namespace App\Providers;

use App\Models\NewsCategory;
use App\Models\PaymentServices;
use Illuminate\Support\ServiceProvider;

class PartialViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('merchant.news.category',function($view){
           $view->with(
               'categories', NewsCategory::CategoriesWithCount()->get()
           );
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
