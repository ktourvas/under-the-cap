<?php

Route::group([ 'middleware' => [ 'api' ] ], function () {

    Route::post('/api/participations', 'UnderTheCap\Http\Controllers\ParticipationsController@submit');

});
