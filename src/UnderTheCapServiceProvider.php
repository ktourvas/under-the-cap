<?php

namespace UnderTheCap;

//use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;

class UnderTheCapServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/under-the-cap.php' => config_path('under-the-cap.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
//        return [  ];
    }
}
