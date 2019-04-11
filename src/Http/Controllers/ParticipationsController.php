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
        $this->validate($request, config('under-the-cap.participation_fields_rules') );
        $data = [];
        foreach ( config('under-the-cap.participation_fields') as $field) {
            $data[$field] = $request->get($field);
        }
        $participation = Participation::create($data);
        return [ 'success' => !empty($participation) ];
    }

}
