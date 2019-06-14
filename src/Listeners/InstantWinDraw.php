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
        if($event->participation->win()->exists()) {

            $draw = $event->participation->promo->draw($event->participation->win[0]->type_id);

            if( !empty($draw) ) {
                if( !empty($draw['mailable']) ) {

//                    \Mail::to(
//                        \App::environment('production') ? '': 'kostas.tourvas@mrm-mccann.gr'
//                    )
//                        ->send(new $draw['mailable']($event->participation));

                }
            }


//            dd($event->participation->promo->draw($event->participation->win[0]->type_id));
//            if(!empty())

        }
        // Access the order using $event->order...

    }
}