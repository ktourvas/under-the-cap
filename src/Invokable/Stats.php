<?php

namespace UnderTheCap\Invokable;

use UnderTheCap\Entities\ParticipationsDay;
use UnderTheCap\Participation;
use UnderTheCap\Promos;

class Stats {

    function __invoke() {
        return $this->fetch();
    }

    protected function fetch() {

        $this->promos = \App::make('UnderTheCap\Promos');

        $result = [];

        foreach( $this->promos->promos() as $index => $promo ) {

            $this->promos->setCurrent($promo->slug());

            $stats = ParticipationsDay::orderBy('date', 'ASC')->get();

            if($promo->status() == 'r' || $this->promos->promos()->count() == 1) {

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
                    ]
                ];

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