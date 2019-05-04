<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        if( !empty( $promo ) ) {
            foreach($this->promo->drawableWinTypes() as $type => $info) {
                $existing = Win::where('type_id', $type)->count();
                if( $existing < $info['number'] ) {
                    $winners = Participation::whereDoesntHave('win')
                        ->inRandomOrder()
                        ->limit( $info['number'] - $existing )
                        ->get();
                    Win::insert(
                        $winners->map(function ($item) use ($type) {
                        return [
                            'participation_id' => $item->id,
                            'type_id' => $type,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    })->toArray()
                    );
                }
            }
        }
        if($request->ajax()) {
            return [ 'success' => true ];
        } else {
            return redirect()->back();
        }
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
