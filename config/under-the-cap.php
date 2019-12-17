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

        'slug' => 'identifier-name',

        /*
        |--------------------------------------------------------------------------
        | Accepting Participation Start Date
        |--------------------------------------------------------------------------
        |
        | This option allows you to specify the date / time after which
        | Participations are being accepted.
        |
        */

        'start_date' => '2019-01-01 00:00:00',

        /*
        |--------------------------------------------------------------------------
        | Accepting Participation End Date
        |--------------------------------------------------------------------------
        |
        | This option allows you to specify the date / time after which
        | Participations are not being accepted.
        |
        */

        'end_date' => '2019-12-01 00:00:00',

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

        'redemption_code_table' => 'utc_redemption_codes',

        'wins_table' => 'utc_wins',

        /*
        |--------------------------------------------------------------------------
        | The types of draws defined for the promo
        |--------------------------------------------------------------------------
        |
        | This option allows you to specify the different types of draws to be run.
        | Two main categories of draws are used,
        | 1. recursive => (for future use with laravel scheduler)
        | 2. adhoc => appearing as options withing the laravel admin extension and can
        | be requested to be performed.
        |
        | Each entry included its
        | 1. title => the type title / label
        | 2. repeat => daily, hourly, etc. for future use with laravel scheduler
        | 3. winners_num the number of winners to be drawn
        | 4. runnerups_num the number of runner ups to be drawn
        | 5. associate_participation_column specify whether the draw should be
        | associated with a specific column's values.
        | Ex. column of user specific choice (see below), will result in three
        | winners, one for each value of the choice column, i.e. three draws each
        | consisting of the sum of participants with the values specified.
        |
        */

        'draws' => [

            1 => [

                'title' => 'the draw label title',

                'type' => '', //adhoc, repeated, instant

                // type: repeat
                'repeat' => 'daily', //daily

                //type: repeat, adhoc
                'winners_num' => 1,
                'runnerups_num' => 2,

                'associate_participation_column' => [
                    'column' => 'choice',
                    'values' => [
                        1, 2, 3
                    ]
                ],

                // type: instant
                'time_start' => '00:00:00',
                'time_end' => '23:59:00',
                'max_winners' => 5,

                //presents
                'limit_presents_by' => 'totals', //totals, daily

                'mailable' => 'MailClass',
                'auto_approved' => true

            ],

        ]
    ]

];
