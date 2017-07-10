<?php

namespace App\Providers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\ServiceProvider;
use App\Exceptions\NormalException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('api.exception')->register(function (AuthorizationException $e) {
            abort(403, '无权操作');
        });

        $this->app->make('api.exception')->register(function (NormalException $e) {
            return response()->json($e->toArray(), 200);
        });
    }
}
