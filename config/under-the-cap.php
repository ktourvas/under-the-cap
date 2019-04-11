<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Accepting Participation Start Date
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify the date / time after which
    | Participations are being accepted.
    |
    */

    'start_date' => time() - 86400,

    /*
    |--------------------------------------------------------------------------
    | Accepting Participation End Date
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify the date / time after which
    | Participations are not being accepted.
    |
    */

    'end_date' => time() + 86400,

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
    | Participation fields validation rules
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify the rules for the participation model
    | fields validation. The array will be populated for use at the submit
    | controller method.
    |
    */

    'participation_fields_rules' => [
        'name' => 'required|max:200',
        'surname' => 'required|max:200',
        'email' => 'required|email',
        'optin' => 'required|accepted'
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
