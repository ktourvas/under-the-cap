<?php

///usr/local/bin/ea-php72 /home/amita/public_html/exohi/artisan schedule:run

namespace mrm\cc3e\amita\eksochi;
use UnderTheCap\Participation;
use UnderTheCap\Win;
use Carbon\CarbonPeriod;
//use mrm\cc3e\amita\eksochi\Mail\DailyWin;
//use mrm\cc3e\amita\eksochi\Mail\DailyDrawNotif;

class DailyParticipations
{
    public function __construct()
    {
        config([ 'under-the-cap.current' => config('under-the-cap.exohi') ]);
    }

    /**
     * Invoke
     *
     * @return void
     */
    public function __invoke()
    {
        if( time() > strtotime('2019-05-01 00:00:00') ) {

            /**
             * This will draw the period between 24/4 - 30/5 with one winner
             * set the date that this will happen and uncomment
             * */
//            if( date('Y-m-d', time()) == '2019-05-01' ) {
//                $this->drawPeriod('2019-04-24','2019-04-30');
//            }

            //Get the dates that will be drawn
            $period = $this->getPeriod();

            //Iterate and draw the dates that should be drawn
            foreach ($period as $date) {
                $this->drawDate($date->format('Y-m-d'));
            }

            if (\App::environment('production')) {
                \Mail::to('kostas.tourvas@mrm-mccann.gr')
                    ->send(new DailyDrawNotif());
            }

        }

    }

    private function getPeriod() {
        //Get the period of time between promo start and current previous day
        $period = CarbonPeriod::create(
        //            date('Y-m-d', config('under-the-cap.current.start_date')),
            '2019-05-01',
            date('Y-m-d', time() - 60 * 60 * 24)
//            date('Y-m-d', strtotime('2019-05-01'))
        );

        //Get an array of Dates drawn
        $doneDates = array_column(
            Win::whereBetween('associated_date', [
                date('Y-m-d', config('under-the-cap.current.start_date')),
                date('Y-m-d', config('under-the-cap.current.end_date'))
            ])->select('associated_date')->get()->toArray()
            , 'associated_date'
        );

        //Filter out the done dates
        $doneFilter = function ($date) use ($doneDates) {
            return !in_array($date->format('Y-m-d'), $doneDates);
        };
        $period->filter($doneFilter);
        return $period;
    }

    private function drawDate($date) {

        //Draw winner
        $participation = Participation::whereDate('created_at', $date)
            ->where('choice', '<>', '0')
            ->whereDoesntHave('win')
            ->whereNotIn('tel', array_column(Participation::whereHas('win')->select('tel')->get()->toArray(), 'tel'))
            ->inRandomOrder()
            ->first();

        if(!empty($participation)) {
            $win = $participation->win()->create([
                'type_id' => 1,
                'associated_date' => $date,
                'confirmed' => 1,
                'runnerup' => 0,
            ]);

            if (\App::environment('production')) {
                \Mail::
//                to('kostas.tourvas@mrm-mccann.gr')
                to($participation->email)
                    ->send(new DailyWin($participation));
            }

        }

        //Draw runnerups
        $participations = Participation::whereDate('created_at', $date)
            ->where('choice', '<>', '0')
            ->whereDoesntHave('win')
            ->whereNotIn('tel', array_column(Participation::whereHas('win')->select('tel')->get()->toArray(), 'tel'))
            ->inRandomOrder()
            ->limit(2)
            ->get();

        foreach ($participations as $participation) {
            $win = $participation->win()->create([
                'type_id' => 1,
                'associated_date' => $date,
                'confirmed' => 1,
                'runnerup' => 1,
            ]);
        }

    }

    private function drawPeriod($sdate, $edate) {

        if(
            Win::whereBetween('associated_date', [ $sdate, $edate ])
                ->select('associated_date')
                ->count() == 0) {

            //Draw winner
            $participation = Participation::whereBetween('created_at', [$sdate, $edate])
                ->where('choice', '<>', '0')
                ->whereDoesntHave('win')
                ->inRandomOrder()
                ->first();
            if (!empty($participation)) {
                $win = $participation->win()->create([
                    'type_id' => 1,
                    'associated_date' => $edate,
                    'confirmed' => 0,
                ]);
                \Mail::
                to('kostas.tourvas@mrm-mccann.gr')
//            to($participation->email)
                    ->send(new DailyWin($participation));
            }

            //Draw runnerups
            $participations = Participation::whereBetween('created_at', [$sdate, $edate])
                ->where('choice', '<>', '0')
                ->whereDoesntHave('win')
                ->inRandomOrder()
                ->limit(2)
                ->get();
            foreach ($participations as $participation) {
                $win = $participation->win()->create([
                    'type_id' => 2,
                    'associated_date' => $edate,
                    'confirmed' => 0,
                ]);
            }

        }

    }

}
