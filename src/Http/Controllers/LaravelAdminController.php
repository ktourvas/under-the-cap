<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UnderTheCap\Participation;
use UnderTheCap\Promo;
use UnderTheCap\Win;

class LaravelAdminController extends Controller {

    public function __construct(Request $request)
    {
        if( !empty( $request->promo ) ) {
            config([ 'under-the-cap.current' => config('under-the-cap.'.$request->promo) ]);
        }
        $this->promo = new Promo();
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
            'promo' => $promo,
            'q' => $request->q,
            'participations' => $participations
        ]);
    }

    public function deleteParticipation($promo, $id) {
        return [ 'success' => Participation::find($id)->delete() ];
    }

    public function draws(Request $request, $promo) {
        return view('utc::admin.wins', [
            'participations' =>
                Win::
                    with('participation')

                    ->orderBy('type_id', 'desc')
                    ->orderBy('associated_date', 'DESC')
                    ->orderBy('runnerup', 'ASC')

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
        //fputcsv($out, array_keys($data[1]));

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
                $line[$field] = $participation[$field];
            }
            $line['created_at'] = $participation->created_at;
            fputcsv($out, $line);
        }
        fclose($out);
    }

}
