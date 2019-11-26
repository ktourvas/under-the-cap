<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UnderTheCap\Entities\Present;

class PresentsController extends Controller {

    protected $promo;

    public function __construct(Request $request)
    {

        if(!empty($request->promo)) {
            \App::make('UnderTheCap\Promos')->setCurrent($request->promo);
            $this->promo = \App::make('UnderTheCap\Promos')->current();
        }

    }

    public function updatePresent(Request $request, Present $present) {
        return [ 'success' => $present->update([
            'daily_give' => $request->daily_give,
            'total_give' => $request->total_give
        ])
        ];
    }

}
