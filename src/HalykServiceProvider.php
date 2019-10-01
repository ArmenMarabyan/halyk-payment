<?php

namespace Studioone\Halyk;

use Illuminate\Support\ServiceProvider;

class HalykServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('halyk', function()
        {
            return new Halyk;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/routes.php';

        $this->loadViewsFrom(__DIR__ . '/Views', 'halyk');
    }
}
