<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\Types\String_;
use UnderTheCap\Entities\Present;
use UnderTheCap\Exceptions\PromoConfigurationException;
use UnderTheCap\Entities\Participation;
use UnderTheCap\Entities\Promo;
use UnderTheCap\Entities\Win;
use Carbon\Carbon;

class WinsController extends Controller {

    protected $promo;

    public function __construct(Request $request)
    {

        if(!empty($request->promo)) {
            \App::make('UnderTheCap\Entities\Promos')->setCurrent($request->promo);
            $this->promo = \App::make('UnderTheCap\Entities\Promos')->current();
        }

    }

    public function addVariant(Request $request, $promo, Win $win, String $token = null) {

        if($win->winpresent->update([
            'variant_id' => $request->variant
        ])) {

            Present::find($win->winpresent->present_id)->variants()->find($request->variant)->decrement('remaining');

            return [
                'success' => true,
                'variant' => $request->variant
            ];

        }

        return [ 'success' => false

//            $win->winpresent->update([
//                'variant_id' => $request->variant
//            ])

        ];
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

    public function delete(Request $request, $promo, Win $win) {

        if( !empty( $win->winpresent->variant ) ) {
            $variant = $win->winpresent->variant;
            $variant->remaining++;
            $variant->save();
        }

        if( !empty( $win->winpresent->present ) ) {
            $present = $win->winpresent->present;
            $present->total_given--;
            $present->save();
        }

        /**
         * The relating relationship deletion can be deprecated
         * and replaced by proper cascading relationship in DB
         */

        $win->winpresent()->delete();

        return [
            'success' => $win->delete()
        ];
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

            if( !empty( $draw['associate_presents'] ) && $draw['associate_presents'] ) {
                $this->assignPresents($draw);
            }

        }

    }

    /**
     * Assign available presents to winners.
     * @param $draw
     */
    private function assignPresents( $draw ) {

        $presents = Present::
            where( 'total_give', '>', \DB::raw('total_given'))
            ->where( 'draw_id', $draw['id'] )
            ->get()
        ;

        foreach( $presents as $present ) {
            $assign = Win::where( 'type_id', $draw['id'] )
                ->where('runnerup', 0)
                ->whereDoesntHave('present')
                ->inRandomOrder()
                ->limit( ($present->total_give - $present->total_given) )
                ->update([
                    'present_id' => $present->id
                ]);

            if($assign > 0) {
                $present->update([
                    'total_given' => ( $present->total_given + $assign )
                ]);
            }
        }

    }

    /**
     * @param $info The information regarding the draw
     * @param $number The number of wins to be drawn
     * @param bool $runnerups Whether to draw of wins or runner ups
     * @param bool $confirmed Whether to flag wins as confirmed
     */
    private function doDraw( $info, $number, $runnerups = false, $confirmed = false ) {

        $wins = $this->pullWins($info);

        $existing = $wins->filter(function ($participation, $key) use ($runnerups) {

            if($runnerups) {
                return $participation->win()->first()->runnerup == 1;
            }
            return $participation->win()->first()->runnerup == 0;

        });

        if( ($number - $existing->count()) > 0) {

            $new = Participation::whereDoesntHave('win', function($q) use ($info)  {
                $q->where('type_id', $info['id']);
            });

            $new = $this->drawSqlAddExcludes($new, $info, $wins);

            $new = $this->drawSqlAddExtras($new, $info);

            $new->inRandomOrder()->limit( ($number - $existing->count()) );

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

            $q->select('id');

            foreach ($info['restrict'] as $column) {

                $q->addSelect($column);

                if($wins->count() > 0) {

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
        whereHas('win', function($q) use ($info) {
            $q->where('type_id', $info['id']);
        })
            ->with(['win' => function ($query) use ($info) {
                $query->where('type_id', $info['id']);
            }]);
        if( !empty($info['extra']) ) {
            foreach ($info['extra'] as $column => $value ) {
                $wins->where($column, $value);
            }
        }
        return $wins->get();
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
        $labels[] = 'Δώρο';
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

            $line['WinPresent'] = !empty($win->present) ? $win->present->title : 'n/a';

            $line['Confirmed'] = $win->confirmed;
            $line['Bumped'] = $win->bumped;
            $line['Wincreated_at'] = $win->created_at;

            fputcsv($out, $line);
        }

        fclose($out);

    }

}
