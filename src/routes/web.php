<?php

use UnderTheCap\Participation;

Route::group([ 'middleware' => [ 'api' ] ], function () {

    Route::post('/api/utc/participations', 'UnderTheCap\Http\Controllers\ParticipationsController@submit');

    Route::post('/api/utc/code', 'UnderTheCap\Http\Controllers\ParticipationsController@submitCode');

    Route::group([ 'middleware' => [
        'auth:api',
        'LaravelAdmin'
    ] ], function () {

        Route::delete('/api/utc/{promo}/participations/{participation}', 'UnderTheCap\Http\Controllers\LaravelAdminController@deleteParticipation')
            ->middleware('can:delete,participation')
        ;

        Route::post('/api/utc/draws/{promo}/download', 'UnderTheCap\Http\Controllers\WinsController@download');

        Route::delete('/api/utc/draws/{promo}/{id}', 'UnderTheCap\Http\Controllers\WinsController@delete');

        Route::put('/api/utc/draws/{promo}/{id}/upgrade', 'UnderTheCap\Http\Controllers\WinsController@upgrade');

        Route::delete('/api/utc/draws/{promo}/{id}/upgrade', 'UnderTheCap\Http\Controllers\WinsController@downgrade');

        Route::post('/api/utc/draws/{promo}', 'UnderTheCap\Http\Controllers\WinsController@draw');

        Route::post('/api/utc/participations/{promo}/download', 'UnderTheCap\Http\Controllers\LaravelAdminController@download');

    });

});

Route::group([ 'middleware' =>

    array_merge(
        [
            'web',
            'LaravelAdmin'
        ],
        ((config('laravel-admin.iprestrict')) ? [ 'ipRestrict' ] : [])
    )

], function () {

    Route::get(config('laravel-admin.root_url').'/utc/participations/{promo}', 'UnderTheCap\Http\Controllers\LaravelAdminController@participations')
        ->middleware('can:viewany,UnderTheCap\Participation');

    Route::get(config('laravel-admin.root_url').'/utc/draws/{promo}', 'UnderTheCap\Http\Controllers\LaravelAdminController@draws')
        ->middleware('can:viewany,UnderTheCap\Win');

});
