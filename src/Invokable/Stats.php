<?php

namespace UnderTheCap\Invokable;

use UnderTheCap\Entities\ParticipationsDay;
use UnderTheCap\Entities\Participation;
use UnderTheCap\Entities\Promos;

class Stats {

    function __invoke() {
        return $this->fetch();
    }

    protected function fetch() {

        $result = [];

        foreach( \App::make('UnderTheCap\Entities\Promos')->promos() as $index => $promo ) {

            \App::make('UnderTheCap\Entities\Promos')->setCurrent($index);

            $stats = ParticipationsDay::orderBy('date', 'ASC')->get();

            $result[] = [
                'title' => $promo->info()['name'],
                'type' => 'graph',
                'width' => 'full',
                'graph' => [
                    'type' => 'line',
                    'source' => '/api/utc/stats/'.$promo->slug(),
                    'data' => [
                        'x' => array_column($stats->toArray(), 'date'),
                        'y' => array_column($stats->toArray(), 'total'),
                    ]
                ],
                'actions' => [
                    'update_daily' => [
                        'label' => 'Update Latest Stats',
                        'method' => 'post',
                        'endpoint' => '/api/utc/stats/'.$promo->slug()
                    ]
                ]
            ];

            if($promo->status() == 'r' || \App::make('UnderTheCap\Entities\Promos')->promos()->count() == 1) {

                $result[] = [
                    'type' => 'number-tile',
                    'title' => $promo->info()['name'].' - Participations total',
                    'number' => Participation::count(),
                    'url' => config('laravel-admin.main_url').'/utc/participations/'.$promo->slug()
                ];

                $result[] = [
                    'type' => 'number-tile',
                    'title' => $promo->info()['name'].' - Participations today',
                    'number' => Participation::whereDate('created_at', date('Y-m-d', time()))->count(),
                    'url' => config('laravel-admin.main_url').'/utc/participations/'.$promo->slug()
                ];

            }

        }
        return $result;
    }

}