<?php

namespace UnderTheCap;

//use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use UnderTheCap\Providers\EventServiceProvider;

class UnderTheCapServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'utc');
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes/web.php';
        }

        $this->publishes([
            __DIR__.'/../config/under-the-cap.php' => config_path('under-the-cap.php'),
        ]);

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

        if(!empty(config('laravel-admin'))) {

            $appends = [];

            foreach(config('under-the-cap') as $promo => $info) {
                if($promo !== 'current') {
                    $appends[$promo] = [
                        'head' => [
                            'label' => $info['name']
                        ],
                        'children' => [
                            [
                                'label' => 'Participations',
                                'url' => config('laravel-admin.main_url').'/utc/participations/'.$promo
                            ],
                            [
                                'label' => 'Draws',
                                'url' => config('laravel-admin.main_url').'/utc/draws/'.$promo
                            ],
                        ]
                    ];
                }
            }

            config([
                'laravel-admin.sidebar_includes' => array_merge(config('laravel-admin.sidebar_includes'), $appends)
            ]);

            config([
                'laravel-admin.dashboard.blocks' => array_merge(
                    config('laravel-admin.dashboard.blocks'),
                    [
                        \UnderTheCap\Invokable\Stats::class,

                    ]
                )
            ]);

        }

        $UTCPromos = new Promos();
        $this->app->instance('UnderTheCap\Promos', $UTCPromos);

        $this->app->register(EventServiceProvider::class);

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'UnderTheCap\Providers\EventServiceProvider'
        ];
    }
}
