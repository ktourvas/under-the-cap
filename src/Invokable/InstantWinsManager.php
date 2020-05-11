<?php

namespace UnderTheCap\Invokable;

use Illuminate\Support\Facades\Storage;
use InstantWin\Player;
use InstantWin\TimePeriod;
use InstantWin\Distribution\EvenOverTimeDistribution;
use UnderTheCap\Entities\InstantsStatus;
use UnderTheCap\Entities\Present;
use UnderTheCap\Entities\Promo;

class InstantWinsManager {

    function __construct() {
        $this->promo = \App::make('UnderTheCap\Entities\Promos')->current();
    }

    function __invoke($id, $info, $participation)
    {
        \DB::raw('LOCK TABLES utc_instants_status WRITE');

        // Pull the available presents. If wins are not related to specific presents use one generic present item
        // with the total of wins expected
        $presents = $this->getPresents($id, $info, $participation);

        $status = $this->getStatus($id, $info);

        if( $presents->count() == 0 ||
            $status->wins >=  $info['max_daily_wins']
        ) {
            \DB::raw('UNLOCK TABLES;');
            return false;
        }

        $status->plays++;
        $win = $this->play( array_merge($status->toArray(), $info) );
        if($win) {
            $status->wins++;
            $present = $presents->random();
            $present->total_given++;
            $present->remaining > 0 ? $present->remaining-- : null;
            $present->save();
        }

        $status->save();
        \DB::raw('UNLOCK TABLES;');

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
        $status = InstantsStatus::where('slug', date('Ymd', time()).'_instant_'.$this->promo->info()['slug'].'_'.$id )->first();
        if( empty($status) ) {
            $status = new InstantsStatus();
            $status->slug = date('Ymd', time()).'_instant_'.$this->promo->info()['slug'].'_'.$id;
            $status->wins = $status->plays = 0;
        }
        return $status;
    }

    protected function getPresents($draw_id, $info, $participation) {
        return Present::where('draw_id', $draw_id)->where(function($q) use ($info, $participation) {

            if( !empty($info['match_participation_present_fields']) ) {
                foreach ($info['match_participation_present_fields'] as $field) {
                    $q->where( $field, $participation[$field] );
                }
            }

            if($info['limit_presents_by'] == 'totals') {
                $q->whereRaw('total_give > total_given');
            }

            if( $info['limit_presents_by'] == 'daily' ) {
                $q->whereRaw('total_given < (daily_give*?)', [$this->promo->dayNumber()]);
            }

            if($info['limit_presents_by'] == 'dailytototals') {
                $q
                    ->whereRaw('total_given < (daily_give*?)', [$this->promo->dayNumber()])
                    ->whereRaw('total_given < total_give')
                ;
            }

            if( $info['limit_presents_by'] == 'remaining' ) {
                $q->whereRaw('remaining > 0');
            }

        })->get();
    }

    protected function checkForWin() {}

}