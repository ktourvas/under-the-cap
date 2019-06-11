<?php

namespace UnderTheCap\Listeners;

use UnderTheCap\Events\ParticipationSubmitted;

class InstantWinDraw
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \UnderTheCap\Events\ParticipationSubmitted  $event
     * @return void
     */
    public function handle(ParticipationSubmitted $event)
    {
//        dd($event);
        // Access the order using $event->order...
    }
}