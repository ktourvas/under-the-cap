<?php

namespace UnderTheCap\Invokable;

use Illuminate\Support\Facades\Storage;
use InstantWin\Player;
use InstantWin\TimePeriod;
use InstantWin\Distribution\EvenOverTimeDistribution;
use UnderTheCap\Entities\Present;
use UnderTheCap\Promo;

class InstantWinsManager {

    function __construct()
    {
//        $this->promo = new Promo();
        $this->promo = \App::make('UnderTheCap\Promos')->current();
    }

    function __invoke($id, $info)
    {

        // Pull the available presents. If wins are not related to specific presents use one generic present item
        // with the total of wins expected
        $presents = $this->getPresents($id, $info);
        $status = $this->getStatus($id, $info);

        if( $presents->count() == 0 || $status['wins'] >=  $info['max_daily_wins'] ) {
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

        Storage::disk()->put(date('Ymd', time()).'_instant_'.$this->promo->info()['slug'].'_'.$id.'.txt', serialize($status));

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
        if( !Storage::disk('local')->exists(date('Ymd', time()).'_instant_'.$this->promo->info()['slug'].'_'.$id.'.txt') ) {
            return array_merge(
                $info,
                [
                    'wins' => 0,
                    'plays' => 0,
                    ]
            );
        } else {
            return unserialize(Storage::disk('local')->get(date('Ymd', time()).'_instant_'.$this->promo->info()['slug'].'_'.$id.'.txt'));
        }
    }

    protected function getPresents($draw_id, $info) {
        return Present::where('draw_id', $draw_id)->where(function($q) use ($info) {
//            'limit_presents_by' => 'totals', //totals, daily, dailytototals
            if($info['limit_presents_by'] == 'totals') {
//                $q->where('total_give', '>', 'total_given');
                $q->whereRaw('total_give > total_given');
            }

            //TODO: validate, test
            if($info['limit_presents_by'] == 'daily') {
//                $this->promo->dayNumber();
                $q->whereRaw('total_given < (daily_give*?)', [$this->promo->dayNumber()]);
            }

            if($info['limit_presents_by'] == 'dailytototals') {
                $q
                    ->whereRaw('total_given < (daily_give*?)', [$this->promo->dayNumber()])
                    ->whereRaw('total_given < total_give')
                ;
            }

        })->get();
    }

    protected function checkForWin() {}

}