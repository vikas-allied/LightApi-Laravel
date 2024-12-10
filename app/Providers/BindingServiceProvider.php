<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BindingServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(
            'App\Services\Service\RoleService',
            'App\Services\Impl\RoleServiceImpl'
        );

    }

        /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
