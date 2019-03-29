<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Participation table name
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify the Participation model database
    | $table.
    |
    */

    'participation_table' => 'participations',

    /*
    |--------------------------------------------------------------------------
    | Participation fields
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify the fields that the participation model
    | will use be able to write. The array will be populated at the $fillable
    | protected variable of the Participation model.
    |
    */

    'participation_fields' => [
        'name',
        'surname',
        'email',
        'tel',
        'optin'
    ],

    /*
    |--------------------------------------------------------------------------
    | RedemptionCode table name
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify the RedemptionCode model database
    | $table.
    |
    */

    'redemption_code_table' => 'redemption_codes',

    /*
    |--------------------------------------------------------------------------
    | The types that a win can be of
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify a list of types that a win can be.
    |
    */
    'win_types' => [
        ''
    ]

];
