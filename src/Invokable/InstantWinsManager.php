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
        // Pull the available presents. If wins are not related to specific presents use one generic present item
        // with the total of wins expected
        $presents = $this->getPresents($id);
        $status = $this->getStatus($id, $info);

        if($presents->count() == 0 || $status['wins'] >=  $info['max_daily_wins'] ) {
            return false;
        }

        $status['plays']++;
        $win = $this->play($status);
        if($win) {
            $status['wins']++;
            $present = $presents->random();
            $present->total_given++;
            $present->save();
        }

        Storage::disk()->put(date('Ymd', time()).'_instant'.$id.'.txt', serialize($status));

        if($win && !empty($present)) {
            return $present;
        }

        return false;

    }

    /**
     * Decides if the player is a winner or not. Returns boolean
     * @param $status
     * @return bool
     */
    protected function play($status) {
        $player = new Player();
        $player->setMaxWins( $status['max_daily_wins'] );
        $player->setCurWins( $status['wins'] );
        $player->setPlayCount( $status['plays'] );
        $timePeriod = new TimePeriod();
        $timePeriod->setStartTimestamp( strtotime( date('Y-m-d', time() ). $status['time_start'] ) );
        $timePeriod->setEndTimestamp( strtotime( date('Y-m-d', time() ). $status['time_end'] ) );
        $timePeriod->setCurrentTimestamp( time() );
        $player->setTimePeriod( $timePeriod );
        $player->setDistribution( new EvenOverTimeDistribution() );
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
                    ]
            );
        } else {
            return unserialize(Storage::disk('local')->get(date('Ymd', time()).'_instant'.$id.'.txt'));
        }
    }

    protected function getPresents($draw_id) {
        return Present::where('draw_id', $draw_id)->where(function($q) {
            $q->where('total_give', '>', 'total_given');
        })->get();
    }

    protected function checkForWin() {}

}