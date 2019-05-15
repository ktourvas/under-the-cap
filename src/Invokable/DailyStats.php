<?php

namespace UnderTheCap\Invokable;

use UnderTheCap\Entities\ParticipationsDay;
use Carbon\CarbonPeriod;
use UnderTheCap\Participation;
use UnderTheCap\Promo;


class DailyStats {

    function __construct()
    {
    }

    function __invoke()
    {
        $this->calc();
    }

    protected function calc() {

        foreach( config('under-the-cap') as $index => $promoInfo ) {

            if( $index != 'current' && !empty($promoInfo['participation_stats_table']) ) {

                config([ 'under-the-cap.current' => config('under-the-cap.'.$index) ]);

                $promo = new Promo();

                $period = CarbonPeriod::create(

                    date('Y-m-d', $promo->info()['start_date']),

                    date('Y-m-d',
                        $promo->info()['end_date'] > (time() - 60 * 60 * 24) ?
                        (time() - 60 * 60 * 24) :
                        $promo->info()['end_date']
                    )
                );

//                //Get an array of calculated dates
                $doneDates = array_column(
                    ParticipationsDay::whereBetween('date', [

                        date('Y-m-d', $promo->info()['start_date']),
                        date('Y-m-d', $promo->info()['end_date'])

                    ])->select('date')->get()->toArray()
                    , 'date'
                );
//
//                //Filter out the done dates
                $doneFilter = function ($date) use ($doneDates) {
                    return !in_array($date->format('Y-m-d'), $doneDates);
                };
                $period->filter($doneFilter);

                foreach ($period as $date) {
                    ParticipationsDay::create([
                        'date' => $date,
                        'total' => Participation::whereDate('created_at', $date)->count()
                    ]);
                }

            }
        }

    }

}