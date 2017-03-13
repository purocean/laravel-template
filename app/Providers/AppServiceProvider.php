<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Wxsdk\Qywx;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->instance('qywx', new Qywx(config('qywx')));
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
