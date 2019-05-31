<?php

namespace UnderTheCap\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use UnderTheCap\Events\ParticipationSubmitted;
use UnderTheCap\Listeners\InstantWinDraw;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //
        ParticipationSubmitted::class => [
            InstantWinDraw::class
        ]
    ];

}