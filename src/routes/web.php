<?php

Route::group([ 'middleware' => [ 'api' ] ], function () {

    Route::post('/api/utc/participations', 'UnderTheCap\Http\Controllers\ParticipationsController@submit');

    /**
     * Route only working on non production environments, for testing needs
     */
    Route::post('/api/utc/participations/truncate', 'UnderTheCap\Http\Controllers\ParticipationsController@truncate');

    Route::post('/api/utc/code', 'UnderTheCap\Http\Controllers\ParticipationsController@submitCode');

    Route::post('/api/utc/{promo}/wins/{win}/variants', 'UnderTheCap\Http\Controllers\WinsController@addVariant');

    Route::group([ 'middleware' => [
        'auth:api',
        'LaravelAdmin'
    ] ], function () {

        Route::delete('/api/utc/{promo}/participations/{participation}', 'UnderTheCap\Http\Controllers\LaravelAdminController@deleteParticipation')
            ->middleware('can:delete,participation');

        Route::post('/api/utc/draws/{promo}/download', 'UnderTheCap\Http\Controllers\WinsController@download');

        Route::delete('/api/utc/draws/{promo}/{win}', 'UnderTheCap\Http\Controllers\WinsController@delete');

        Route::put('/api/utc/draws/{promo}/{id}/upgrade', 'UnderTheCap\Http\Controllers\WinsController@upgrade');

        Route::delete('/api/utc/draws/{promo}/{id}/upgrade', 'UnderTheCap\Http\Controllers\WinsController@downgrade');

        Route::post('/api/utc/draws/{promo}', 'UnderTheCap\Http\Controllers\WinsController@draw');

        Route::post('/api/utc/participations/{promo}/download', 'UnderTheCap\Http\Controllers\LaravelAdminController@download');

        Route::post('/api/utc/presents/{present}', 'UnderTheCap\Http\Controllers\PresentsController@updatePresent');

        Route::post('/api/utc/stats/{promo}', 'UnderTheCap\Http\Controllers\StatsController@updateDaily');

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
        ->middleware('can:viewany,UnderTheCap\Entities\Participation');

    Route::get(config('laravel-admin.root_url').'/utc/draws/{promo}', 'UnderTheCap\Http\Controllers\LaravelAdminController@draws')
        ->middleware('can:viewany,UnderTheCap\Entities\Win');

    Route::get(config('laravel-admin.root_url').'/utc/codes/{promo}', 'UnderTheCap\Http\Controllers\LaravelAdminController@codes')
        ->middleware('can:viewany,UnderTheCap\Entities\RedemptionCode');

    Route::get(config('laravel-admin.root_url').'/utc/presents/{promo}', 'UnderTheCap\Http\Controllers\LaravelAdminController@presents')
        ->middleware('can:viewany,UnderTheCap\Entities\Present');
    ;

});
