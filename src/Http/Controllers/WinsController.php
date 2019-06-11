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

    public function draw(Request $request) {

        if(!empty($request->draw)) {

            $draw = array_merge($this->promo->draw($request->draw), [
                'id' => $request->draw
            ]);

            if( !empty($draw['associate_participation_column']) && !empty($draw['associate_participation_column']['values']) ) {

                if(count($draw['associate_participation_column']['values']) !== $draw['winners_num']) {
                    throw new PromoConfigurationException('Associated column values count does not match the number of winners.');
                }

                foreach ($draw['associate_participation_column']['values'] as $value) {

                    $draw['extra'] = [
                        $draw['associate_participation_column']['column'] => $value
                    ];

                    $this->doDraw($draw, 1, false, false);

                    $this->doDraw($draw, $draw['runnerups_num'],true, false);

                }

            } else {

                $this->doDraw($draw, $draw['winners_num'], false, false);

                $this->doDraw($draw, $draw['runnerups_num'], true, false);

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
     * @param $info The information regarding the draw
     * @param $number The number of wins to be drawn
     * @param bool $runnerups Whether to draw of wins or runner ups
     * @param bool $confirmed Whether to flag wins as confirmed
     */
    private function doDraw( $info, $number, $runnerups = false, $confirmed = false ) {

        $wins = $this->pullWins($info);

        $existingCount = $wins->map(function($participation) {
            return $participation;
        })->reject(function($participation) use ($runnerups) {
            return $runnerups ? $participation->win()->first()->runnerup == 0 : $participation->win()->first()->runnerup == 1;
        })->count();

        if( ($number - $existingCount) > 0) {

            $new = Participation::whereDoesntHave('win', function($q) use ($info)  {
                $q->where('type_id', $info['id']);
            });

            $new = $this->drawSqlAddExcludes($new, $info, $wins);

            $new = $this->drawSqlAddExtras($new, $info);

            $new->inRandomOrder()->limit( ($number - $existingCount) );

            $new = $new->get();

            $new->map(function($participation) use ($info, $runnerups, $confirmed) {
                $participation->win()->create([
                    'type_id' => $info['id'],
                    'runnerup' => $runnerups ? 1 : 0,
                    'confirmed' => $confirmed ? 1 : 0,
                ]);
            });

        }
    }

    private function drawSqlAddExtras($q, $info) {
        if( !empty( $info['extra'] ) ) {
            foreach ( $info['extra'] as $column => $value ) {
                $q->where($column, $value);
            }
        }
        return $q;
    }

    /**
     * @param $q
     * @param $info
     * @param $wins
     * @return mixed
     */
    private function drawSqlAddExcludes($q, $info, $wins) {
        if( !empty($info['restrict']) ) {

            if($wins->count() > 0) {

                foreach ($info['restrict'] as $column) {
                    $excludes = $wins->map(function ($participation) use ($column) {
                        return $participation[$column];
                    })->toArray();
                    $q->whereNotIn($column, $excludes);
                }

            }

            $q->distinct();
        }
        return $q;
    }

    /**
     * Pull all the wins of a given type filtered with associated columns if any.
     * Ex. if we are drawing for a choice of present that a user has submitted with his participation.
     *
     * @param $type
     * @param $extra
     * @return mixed
     */
    private function pullWins($info) {
        $wins = Participation::

        where(function($q) use ($info) {
            if( !empty($info['extra']) ) {
                foreach ($info['extra'] as $column => $value ) {
                    $q->where($column, $value);
                }
            }
        })
            ->whereHas('win', function($q) use ($info) {
                $q->where('type_id', $info['id']);
            })
            ->with('win')
//            ->toSql()
            ->get()
        ;
        return $wins;
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
