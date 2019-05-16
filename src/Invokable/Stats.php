<?php

namespace UnderTheCap\Invokable;

use UnderTheCap\Entities\ParticipationsDay;

class Stats {

    function __construct()
    {
    }

    function __invoke()
    {
        return $this->fetch();
    }

    protected function fetch() {

        $result = [];

        foreach( config('under-the-cap') as $index => $promoInfo ) {

            if( $index != 'current' && !empty($promoInfo['participation_stats_table']) ) {

                $stats = ParticipationsDay::orderBy('date', 'ASC')->get();

                $result[] = [
                    'title' => $promoInfo['name'],

                    'type' => 'graph',

                    'width' => 'full',

                    'graph' => [
                        'type' => 'line',
                        'source' => '/api/utc/stats/'.$promoInfo['slug'],
                        'data' => [
                            'x' => array_column($stats->toArray(), 'date'),
                            'y' => array_column($stats->toArray(), 'total'),
                        ]
                    ]
                ];


            }
        }

        return $result;
    }

}