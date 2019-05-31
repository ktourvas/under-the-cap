<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UnderTheCap\Events\ParticipationSubmitted;
use UnderTheCap\Exceptions\RedemptionCodeException;
use UnderTheCap\Participation;
use UnderTheCap\Promo;
use UnderTheCap\RedemptionCode;

class ParticipationsController extends Controller {

    protected $promo;

    public function __construct(Request $request)
    {
        if(!empty($request->utc_env)) {
            if( !empty( config('under-the-cap.'.$request->utc_env) ) ) {
                config([ 'under-the-cap.current' => config('under-the-cap.'.$request->utc_env) ]);
            }
        }
        $this->promo = new Promo();
    }

    public function submitCode(Request $request) {
        return $this->submit($request, 'redemption');
    }

    public function submit(Request $request, $submissionType = 'sole')
    {
        //Start by validating the promo status. Throws PromoStatusException if status !== r
        $this->promo->validatePromoStatus();

        // Validate the form submission. Despite of the type of submission a single set of validation rules is
        // used for convenience.
        $this->validate($request,
            $this->promo->participationValidationRules()->toArray(),
            $this->promo->participationValidationMessages()->toArray()
        );

        // If the submission type includes code redemption, validate the code of the submission
        if( $submissionType == 'redemption' ) {
            $code = null;
            if( !empty($request->code) ) {
                $code = $this->getRedemptionCode($request->code);
                if(empty($code)) {
                    throw new RedemptionCodeException($this->promo);
                }
            }
        }

        // Filter the promo participation fields excluding the code and anything else needed in future updates
        $fields = collect($this->promo->participationFieldKeys())->reject(function ($field) {
            return $field === 'code';
        })
            ->map(function ($field) {
                return $field;
            });

        // Create the array for participation creation and feed it to the model create method
        $create = [];
        foreach ( $fields as $field) {
            $create[$field] = $request->get($field);
        }
//        foreach ( $this->promo->participationFieldKeys() as $field) {
//            $create[$field] = $request->get($field);
//        }

        // Check if the request comes with
        if(\Auth::guest()) {
            $participation = Participation::create($create);
        } else {
            // Remember User has to use UnderTheCap/Participant
            $participation = $request->user()->participations()->create($create);
        }


        // If a code has been validated, assign it to the participation
        if( !empty($code) ) {
            $participation->redemptionCode()->associate($code);
            $participation->save();
        }

        // All went well, emit the ParticipationSubmitted event
        if(!empty($participation)) {
            event( new ParticipationSubmitted($participation) );
        }

        return [ 'success' => !empty($participation) ];

    }

    /**
     * Retrieve a redemption code by code
     * @param $code
     * @return mixed
     */
    public function getRedemptionCode($code) {
        return RedemptionCode::where('code', $code)->whereDoesntHave('participation')->first();
    }

}
