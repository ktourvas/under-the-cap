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
    |
    | ex. config([ 'under-the-cap.current' => config('under-the-cap.mypreference') ]);
    |
    | When the package provided routes are used, make sure to include a variable
    | with the name utc_env in order for the correct config to be used.
    | In case your application manages one promo environment use the existing
    | setup with one root key with the name "current"
    |
    */

    'current' => [

        'name' => 'Identifier Name',

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

        'participation_table' => 'utc_participations',

        /*
        |--------------------------------------------------------------------------
        | Participation fields
        |--------------------------------------------------------------------------
        |
        | This option allows you to specify the fields that the participation model
        | will use. Each field item may include the is_searchable & is_id booleans,
        | a tile to be used for lists and table views, its validation rules and
        | validation error messages. The keys of the main array must be the same as
        | the equivalent database column names and are used in the fillable variable
        | of the Participation Eloquent Model.
        |
        */

        'participation_fields' => [
            'name' => [
                'title' => 'Όνομα',
                'is_searchable' => true,
                'rules' => 'required|max:200',
                'messages' => [
                    'name.required' => 'Το πεδίο Όνομα είναι υποχρεωτικό',
                ],
            ],
            'surname' => [
                'title' => 'Επώνυμο',
                'is_searchable' => true,
                'rules' => 'required|max:200',
                'messages' => [
                    'surname.required' => 'Το πεδίο Επώνυμο είναι υποχρεωτικό',
                ],
            ],
            'email' => [
                'title' => 'Email',
                'is_searchable' => true,
                'rules' => 'required|email',
                'messages' => [
                    'email.required' => 'Το πεδίο E-mail είναι υποχρεωτικό',
                    'email.unique' => 'Υπάρχει ήδη συμμετοχή με την συγκεκριμένη e-mail διεύθυνση',
                ],
            ],
            'tel' => [
                'title' => 'Τηλέφωνο',
                'is_searchable' => true,
                'rules' => 'nullable|digits:10|starts_with:69',
                'messages' => [
                    'tel.digits' => 'Παρακαλούμε συμπλήρωσε ένα σωστό κινητό τηλέφωνο επικοινωνίας',
                    'tel.starts_with' => 'Παρακαλούμε συμπλήρωσε ένα σωστό κινητό τηλέφωνο επικοινωνίας',
                ],
            ],

            'optin' => [
                'title' => 'Opt In',
                'rules' => 'required|accepted',
                'messages' => [],
            ],

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

        'wins_table' => 'utc_wins',

        /*
        |--------------------------------------------------------------------------
        | The types that a win can be of
        |--------------------------------------------------------------------------
        |
        | This option allows you to specify a list of types that a win can be of.
        | Each entry included its
        | 1. title => the type title / label
        | 2. drawable => bool, flags the type as usable with draw requests
        | 3. bumpable => bool, flags a type as being able to be flagged as upgraded.
        |    Ex. when a runner up needs to take the place of a winner.
        | 4. number => the number of total Participations to draw for the specific
        | type.
        |
        */
        'win_types' => [

            1 => [
                'title' => 'Winner',
                'drawable' => true,
                'bumpable' => false,
                'number' => 1,
            ],

            2 => [
                'title' => 'Runner Up',
                'drawable' => true,
                'bumpable' => true,
                'number' => 3,
            ],

        ]
    ]

];
