<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UnderTheCap\Events\ParticipationSubmitted;
use UnderTheCap\Exceptions\PromoStatusException;
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

    /**
     * Manage the participation submission.
     *
     * @return array
     * @throws PromoStatusException
     */
    public function submit(Request $request)
    {
        $this->promo->validatePromoStatus();

        $this->validate($request,
            $this->promo->participationValidationRules()->toArray(),
            $this->promo->participationValidationMessages()->toArray()
        );

        $create = [];
        foreach ( $this->promo->participationFieldKeys() as $field) {
            $create[$field] = $request->get($field);
        }

        $participation = Participation::create($create);

        if(!empty($participation)) {
            event( new ParticipationSubmitted($participation) );
        }

        return [ 'success' => !empty($participation) ];

    }

}
