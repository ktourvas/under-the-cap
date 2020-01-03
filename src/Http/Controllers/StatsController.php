<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use UnderTheCap\Invokable\DailyStats;

class StatsController extends Controller {

    public function __construct(Request $request) {}

    public function updateDaily(Request $request) {
        $st = new DailyStats();
        return [ 'success' => $st() ];
    }

}
