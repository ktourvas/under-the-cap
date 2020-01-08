<?php

namespace UnderTheCap\Invokable;

use UnderTheCap\Entities\ParticipationsDay;
use Carbon\CarbonPeriod;
use UnderTheCap\Entities\Participation;
use UnderTheCap\Entities\Promo;


class DailyStats {

    function __construct()
    {

    }

    function __invoke() {
        $this->calc();
    }

    protected function calc() {

        foreach( config('under-the-cap') as $index => $promoInfo ) {

            \App::make('UnderTheCap\Entities\Promos')->setCurrent($index);

            if( $index != 'current' && !empty($promoInfo['participation_stats_table']) ) {

                $period = CarbonPeriod::create(

                    date('Y-m-d', \App::make('UnderTheCap\Entities\Promos')->current()->info()['start_date']),

                    date('Y-m-d',
                        \App::make('UnderTheCap\Entities\Promos')->current()->info()['end_date'] > (time() - 60 * 60 * 24) ?
                        (time() - 60 * 60 * 24) :
                            \App::make('UnderTheCap\Entities\Promos')->current()->info()['end_date']
                    )
                );

                /**
                 * Get an array of calculated dates
                 */
                $doneDates = array_column(
                    ParticipationsDay::whereBetween('date', [

                        date('Y-m-d', \App::make('UnderTheCap\Entities\Promos')->current()->info()['start_date']),
                        date('Y-m-d', \App::make('UnderTheCap\Entities\Promos')->current()->info()['end_date'])

                    ])->select('date')->get()->toArray()
                    , 'date'
                );

                /**
                 * Filter out the done dates
                 * @param $date
                 * @return bool
                 */
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