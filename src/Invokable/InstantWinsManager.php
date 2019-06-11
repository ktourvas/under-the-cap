<?php

namespace UnderTheCap\Invokable;

use Illuminate\Support\Facades\Storage;
use InstantWin\Player;
use InstantWin\TimePeriod;
use InstantWin\Distribution\EvenOverTimeDistribution;
use UnderTheCap\Entities\Present;

class InstantWinsManager {

    function __construct()
    {
    }

    function __invoke($id, $info)
    {

        $status = $this->getStatus($id, $info);
//        dd($status);
        if( $status['max_winners'] == $status['wins'] ) {
            return false;
        }

        $win = $this->play($status);

        $status['plays']++;

        if($win) {

            $status['wins']++;

            $available = $status['presents']->map(function($item, $key) {
                return $item;
            })->reject(function($item, $key) use ($status) {

                switch ($status['limit_presents_by']) {
                    case 'totals':
                        return ($item['given'] == $item['total'])
                            || ($item['daily_count'] > 0 && $item['given'] == $item['daily_count']);
                        break;
                    case 'daily':
                        return $item['given'] == $item['daily_count'];
                        break;
                }

            });

            $present = $available->random();

            $status['presents'] = $status['presents']->map(function($item, $key) use ($present) {
                if( $item['id'] == $present['id'] ) {
                    $item['given']++;
                }
                return $item;
            });

        }

        Storage::disk()->put(date('Ymd', time()).'_instant'.$id.'.txt', serialize($status));

    }

    /**
     * Decides if the player is a winner or not. Returns boolean
     * @param $status
     * @return bool
     */
    protected function play($status) {
        $player = new Player();
        $player->setMaxWins($status['max_winners']);
        $player->setCurWins($status['wins']);
        $player->setPlayCount($status['plays']);
        $timePeriod = new TimePeriod();
        $timePeriod->setStartTimestamp( strtotime( date('Y-m-d', time() ). $status['time_start'] ) );
        $timePeriod->setEndTimestamp( strtotime( date('Y-m-d', time() ). $status['time_end'] ) );
        $timePeriod->setCurrentTimestamp( time() );
        $player->setTimePeriod( $timePeriod );
        $player->setDistribution(new EvenOverTimeDistribution());
        return $player->isWinner();
    }

    /**
     * Returns the current instant wins status for the given promo found in storage or creates a new set of information
     * @param $id
     * @param $info
     * @return array|mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStatus($id, $info) {
        if( !Storage::disk('local')->exists(date('Ymd', time()).'_instant'.$id.'.txt') ) {
            return array_merge(
                $info,

                [

                'wins' => 0,
                'plays' => 0,
                'presents' => collect($info['presents'])->map(function ($item, $key) {
                    if(empty($item['given'])) {
                        $item['given'] = 0;
                    }
                    return $item;
                })

            ]);
        } else {
            return unserialize(Storage::disk('local')->get(date('Ymd', time()).'_instant'.$id.'.txt'));
        }
    }

    protected function checkForWin() {

    }

}