<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UnderTheCap\Entities\Present;
use UnderTheCap\Entities\Participation;
use UnderTheCap\Entities\Promo;
use UnderTheCap\Entities\RedemptionCode;
use UnderTheCap\Entities\Win;

class LaravelAdminController extends Controller {

    public function __construct(Request $request)
    {

        if(!empty($request->promo)) {
            \App::make('UnderTheCap\Entities\Promos')->setCurrent($request->promo);
            $this->promo = \App::make('UnderTheCap\Entities\Promos')->current();
        }

    }

    public function presents(Request $request, $promo) {
        $presents = Present::orderBy('created_at', 'desc')->paginate('50');
        return view('utc::admin.presents', [
            'promo' => $this->promo,
            'presents' => $presents
        ]);
    }

    public function participations(Request $request, $promo) {
        $participations = Participation::orderBy('created_at', 'desc')->where(function($q) use ($request) {
            /**
             * if a search query is part of the request, get searchable fields and add relevant query clauses
             * TODO: make selection fields labels searchable
             */
            if(!empty($request->q)) {
                foreach (array_keys($this->promo->ParticipationSearchables()->toArray()) as $field) {
                    $q->orWhere($field, 'LIKE', '%'.$request->q.'%');
                }
            }

        })->paginate('50');

        return view('utc::admin.participations', [
            'promo' => $this->promo,
            'q' => $request->q,
            'participations' => $participations
        ]);
    }

    public function deleteParticipation($promo, Participation $participation) {

        return [ 'success' => $participation->delete() ];

    }

    public function draws(Request $request, $promo) {
        return view('utc::admin.wins', [
            'participations' =>
                Win::
                    with('participation')

                    ->orderBy('type_id', 'desc')

                    ->orderBy('runnerup', 'asc')

                    ->orderBy('created_at', 'desc')

                    ->orderBy('associated_date', 'desc')

                    ->get(),
//            'promo' => $promo,
//            'wintypes' => $this->promo->winTypes(),
            'promo' => $this->promo
        ]);
    }

    public function download(Request $request, $promo) {
        ini_set ( 'max_execution_time', 120 );
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="'.$promo.'.participations_'.time().'.csv"');
        $data = Participation::orderBy('created_at', 'DESC')->get();
        $out = fopen('php://output', 'w');
//        //fputcsv($out, array_keys($data[1]));

        $labels = [ 'ID' ];
        foreach ( $this->promo->participationFields() as $field => $info ) {
            $labels[] = $info['title'];
        }
        $labels[] = 'created at';
        fputcsv($out, $labels);
        foreach($data as $participation) {
            $line = array();
            $line['id'] = $participation->id;
            foreach ( $this->promo->participationFields() as $field => $info ) {
                if( !empty($info['relation']) ) {
                    $line[$field] = $participation[$info['relation'][0]][$info['relation'][1]];
                } else {
                    $line[$field] = $participation[$field];
                }
            }
            $line['created_at'] = $participation->created_at;
            fputcsv($out, $line);
        }
        fclose($out);
    }

    public function codes(Request $request, $promo) {
        $codes = RedemptionCode::orderBy('id', 'asc')->where(function($q) use ($request) {
            if(!empty($request->q)) {
                $q->where('code', 'LIKE', '%'.$request->q.'%');
            }

        })->paginate('50');
        return view('utc::admin.redemptioncodes', [
            'promo' => $this->promo,
            'q' => $request->q,
            'codes' => $codes
        ]);
    }

}
