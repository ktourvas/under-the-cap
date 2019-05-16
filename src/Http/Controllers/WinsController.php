<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UnderTheCap\Exceptions\PromoConfigurationException;
use UnderTheCap\Participation;
use UnderTheCap\Promo;
use UnderTheCap\Win;
use Carbon\Carbon;

class WinsController extends Controller {

    public function __construct(Request $request)
    {
        if( !empty( $request->promo ) ) {
            config([ 'under-the-cap.current' => config('under-the-cap.'.$request->promo) ]);
        }
        $this->promo = new Promo();
    }

    public function upgrade(Request $request, $promo, $id) {
        return [ 'success' => Win::find($id)->update([
            'bumped' => 1
        ]) ];
    }

    public function downgrade(Request $request, $promo, $id) {
        return [ 'success' => Win::find($id)->update([
            'bumped' => 0
        ]) ];
    }

    public function delete(Request $request, $promo, $id) {
        return [ 'success' => Win::find($id)->delete() ];
    }

    public function draw(Request $request, $promo) {

        if(!empty($request->draw)) {

            $draw = $this->promo->draw($request->draw);

            if( !empty($draw['associate_participation_column']) && !empty($draw['associate_participation_column']['values']) ) {

                if(count($draw['associate_participation_column']['values']) !== $draw['winners_num']) {
                    throw new PromoConfigurationException('Associated column values count does not match the number of winners.');
                }

                foreach ($draw['associate_participation_column']['values'] as $value) {

                    $this->doDraw($request->draw, 1,
                        [
                            $draw['associate_participation_column']['column'] => $value
                        ],
                        (!empty($draw['restrict']) ? $draw['restrict'] : []),
                        false);

                    $this->doDraw($request->draw, $draw['runnerups_num'],
                        [
                            $draw['associate_participation_column']['column'] => $value
                        ],
                        (!empty($draw['restrict']) ? $draw['restrict'] : []),
                        true);

                }


            } else {

                $this->doDraw(
                    $request->draw,
                    $draw['winners_num'],
                    [],
                    (!empty($draw['restrict']) ? $draw['restrict']: []),
                    false);
                $this->doDraw(
                    $request->draw,
                    $draw['runnerups_num'],
                    [],
                    (!empty($draw['restrict']) ? $draw['restrict']: []),
                    true);

            }

        }

//        if( !empty( $promo ) ) {
//            foreach($this->promo->drawableWinTypes() as $type => $info) {
//                $existing = Win::where('type_id', $type)->count();
//                if( $existing < $info['number'] ) {
//                    $winners = Participation::whereDoesntHave('win')
//                        ->inRandomOrder()
//                        ->limit( $info['number'] - $existing )
//                        ->get();
//                    Win::insert(
//                        $winners->map(function ($item) use ($type) {
//                        return [
//                            'participation_id' => $item->id,
//                            'type_id' => $type,
//                            'created_at' => Carbon::now(),
//                            'updated_at' => Carbon::now(),
//                        ];
//                    })->toArray()
//                    );
//                }
//            }
//        }
//        if($request->ajax()) {
//            return [ 'success' => true ];
//        } else {
//            return redirect()->back();
//        }
    }

    /**
     * TODO: refactor => two params, 1. ID, 2. info
     * TODO: clear up process logic
     *
     * @param $type
     * @param $number
     * @param array $extra
     * @param array $restrict
     * @param bool $runnerups
     * @param null $associated_date
     * @param bool $confirmed
     * @return null
     */

    private function doDraw( $type, $number, $extra = [], $restrict = [], $runnerups = false, $associated_date = null, $confirmed = false ) {

        $wins = Participation::

        where(function($q) use ($extra) {
            if( !empty($extra) ) {
                foreach ($extra as $column => $value ) {
                    $q->where($column, $value);
                }
            }
        })
            ->whereHas('win', function($q) use ($type) {
                $q->where('type_id', $type);
            })
            ->with('win')
            ->get();

        $existingCount = $wins->map(function($participation) {
            return $participation;
        })->reject(function($participation) use ($runnerups) {
            return $runnerups ? $participation->win()->first()->runnerup == 0 : $participation->win()->first()->runnerup == 1;
        })->count();

        /**
         * if excludes are set up, retrieve all wins and update the excludes array
         */
        $excludes = [];

        if(!empty($restrict) && $wins->count() > 0) {
            foreach ($restrict as $column) {
                $excludes[$column] = [];
                $excludes[$column] = $wins->map(function($participation) use ($column, $excludes) {
                    return $participation[$column];
                })->toArray();
            }
        }

        if( ($number - $existingCount) > 0) {

            $new = Participation::where(function($q) {

            })
                ->whereDoesntHave('win', function($q)use ($type)  {
                    $q->where('type_id', $type);
                });

            if( !empty($extra) ) {
                $new->where(function($q) use ($extra) {
                    foreach ($extra as $column => $value ) {
                        $q->where($column, $value);
                    }
                });
            }

            $new->inRandomOrder()->limit( ($number - $existingCount) );

            if(!empty($restrict)) {
                $new->distinct();
            }

            $new = $new->get();

            $new->map(function($participation) use ($type, $runnerups, $confirmed) {
                $participation->win()->create([
                    'type_id' => $type,
                    'runnerup' => $runnerups ? 1 : 0,
                    'confirmed' => $confirmed ? 1 : 0,
                ]);
            });

        }
        return null;
    }



    public function download(Request $request, $promo) {

        ini_set ( 'max_execution_time', 120 );
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="'.$promo.'.wins_'.time().'.csv"');

        $data = Win::orderBy('type_id', 'desc')
            ->with('participation')
            ->get();

        $out = fopen('php://output', 'w');
        //fputcsv($out, array_keys($data[1]));

        $labels = [ 'ID' ];
        foreach ($this->promo->participationFields() as $field => $info) {
            $labels[] = $info['title'];
        }

        $labels[] = 'created at';
        $labels[] = 'Τύπος Νίκης';
        $labels[] = 'Ημερομηνία Νίκης';
        $labels[] = 'Approved';
        $labels[] = 'Upgraded';
        $labels[] = 'Ημ./Ώρα Δημιουργίας Νίκης';

        fputcsv($out, $labels);

        foreach($data as $win) {
            $line = array();
            $line['id'] = $win->participation->id;

            foreach ($this->promo->participationFields() as $field => $info) {
                $line[$field] = $win->participation[$field];
            }
            $line['created_at'] = $win->participation->created_at;

            $line['WinType'] = $win->type_name;
            $line['WinAssociatedDate'] = $win->associated_date;
            $line['Confirmed'] = $win->confirmed;
            $line['Bumped'] = $win->bumped;
            $line['Wincreated_at'] = $win->created_at;

            fputcsv($out, $line);
        }

        fclose($out);

    }

}
