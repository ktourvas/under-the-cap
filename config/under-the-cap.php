<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Promo Configuration Variables Group
    |--------------------------------------------------------------------------
    |
    | If your laravel installation runs more than one separate promos, assign
    | use multiple copies of the configuration variables with different names
    | at the root array level representing each promo. The one used each time
    | a class is called is the one with the key "current". This means that
    | depending on your setup your application is responsible for setting the
    | current key to the correct one for proper functionality.
    | When the package provided routes are used, make sure to include a variable
    | with the name utc_env in order for the correct config to be used.
    | In case your application manages one promo environment use the existing
    | setup with one root key with the name "current"
    |
    */

    'current' => [

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
    ]

];
