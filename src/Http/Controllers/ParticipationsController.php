<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UnderTheCap\Participation;

class ParticipationsController extends Controller {

    /**
     * Manage the contact form post submission.
     *
     * @return void
     */
    public function submit(Request $request)
    {
        if(!empty($request->utc_env)) {
            if( !empty( config('under-the-cap.'.$request->utc_env) ) ) {
                config([ 'under-the-cap.current' => config('under-the-cap.'.$request->utc_env) ]);
            }
        }

        if(
            !empty( config('under-the-cap.current.start_date') ) &&
            !empty( config('under-the-cap.current.end_date') )
        ) {
            if(
                time() < !empty( config('under-the-cap.current.start_date') )
                ||
                time() > !empty( config('under-the-cap.current.end_date') )
            ) {
                return response()->json([ 'error' => '' ], 503);
            }
        }

        $this->validate($request, config('under-the-cap.current.participation_fields_rules') );
        $data = [];
        foreach ( config('under-the-cap.current.participation_fields') as $field) {
            $data[$field] = $request->get($field);
        }
        $participation = Participation::create($data);
        return [ 'success' => !empty($participation) ];
    }

}
