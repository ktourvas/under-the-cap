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
        if( $event->participation->promo->instantDraws()->count() > 0 ) {
            if($event->participation->win()->exists()) {
                $draw = $event->participation->promo->draw($event->participation->win[0]->type_id);
                if( !empty($draw) ) {

                    //Using mailable
                    if( !empty($draw['mailable']) ) {
                        \Mail::to(
                            \App::environment('production') ? $event->participation->email : $draw['testnotificationsrecepient']
                        )
                            ->send(new $draw['mailable']($event->participation));
                    }

                    //Using notifiable trait
                    if( !empty($draw['notification']) ) {
                        $event->participation->notify(
                            new $draw['notification']()
                        );
                    }

                }
            }
        }
    }
}