<?php

namespace UnderTheCap\Invokable;

use UnderTheCap\Entities\ParticipationsDay;
use UnderTheCap\Participation;

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

                $result[] = [
                    'type' => 'number-tile',
                    'title' => 'Participations total',
                    'number' => Participation::count(),
                    'url' => 'utc/participations/exohi',
                ];

                $result[] = [
                    'type' => 'number-tile',
                    'title' => 'Participations today',
                    'number' => Participation::whereDate('created_at', date('Y-m-d', time()))->count(),
                    'url' => 'utc/participations/exohi',
                ];

            }
        }

        return $result;
    }

}