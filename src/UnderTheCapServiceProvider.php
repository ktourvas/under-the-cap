<?php

namespace UnderTheCap;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use UnderTheCap\Entities\Participation;
use UnderTheCap\Entities\Policies\ParticipationPolicy;
use UnderTheCap\Entities\Policies\PresentPolicy;
use UnderTheCap\Entities\Policies\PromoPolicy;
use UnderTheCap\Entities\Policies\RedemptionCodePolicy;
use UnderTheCap\Entities\Policies\WinPolicy;
use UnderTheCap\Entities\Present;
use UnderTheCap\Entities\RedemptionCode;
use UnderTheCap\Entities\Win;
use UnderTheCap\Providers\EventServiceProvider;
use UnderTheCap\Entities\Promos;
use UnderTheCap\Entities\Promo;

class UnderTheCapServiceProvider extends ServiceProvider
{

    protected $policies = [
        Promo::class => PromoPolicy::class,
        Participation::class => ParticipationPolicy::class,
        Present::class => PresentPolicy::class,
        RedemptionCode::class => RedemptionCodePolicy::class,
        Win::class => WinPolicy::class
    ];

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

        $this->registerPolicies();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if( !empty( config('laravel-admin') ) ) {

            $appends = [];

            if( config('under-the-cap') !== null ) {

                foreach(config('under-the-cap') as $promo => $info) {

                    if($promo !== 'current') {

                        $append = [
                            'head' => [
                                'label' => $info['name']
                            ],
                            'children' => [
                                [
                                    'label' => 'Participations',
                                    'url' => config('laravel-admin.main_url').'/utc/participations/'.$promo
                                ],
                                [
                                    'label' => 'Presents',
                                    'url' => config('laravel-admin.main_url').'/utc/presents/'.$promo
                                ]
                            ],
                            'authorize' => [
                                [
                                    'view',
                                    new Promo( $info )
                                ]
                            ]
                        ];

//                        'redemption_code_table' => 'motion_listenup_redemption_codes',
                        if( !empty($info['redemption_code_table']) ) {
                            $append['children'][] = [
                                'label' => 'Codes',
                                'url' => config('laravel-admin.main_url').'/utc/codes/'.$promo
                            ];
                        }

                        if( !empty($info['draws']) ) {
                            $append['children'][] = [
                                'label' => 'Draws',
                                'url' => config('laravel-admin.main_url').'/utc/draws/'.$promo
                            ];
                        }

                        $appends[$promo] = $append;

                    }
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

        $this->app->instance('UnderTheCap\Entities\Promos', new Promos());

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

    private function registerPolicies() {
        if( intval( explode( ".", $this->app->version() )[0]) >= 5 ||
            intval(explode( ".", $this->app->version() )[1] ) >= 8  ) {
            foreach ($this->policies as $key => $value) {
                Gate::policy($key, $value);
            }
        }
    }
}
