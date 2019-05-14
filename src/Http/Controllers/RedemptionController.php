<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UnderTheCap\Exceptions\PromoStatusException;
use UnderTheCap\Exceptions\RedemptionCodeException;
use UnderTheCap\Participation;
use UnderTheCap\Promo;
use UnderTheCap\RedemptionCode;

class RedemptionController extends Controller {

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
     * Manage the participation redemption code submission.
     *
     * @return array
     * @throws PromoStatusException
     * @throws RedemptionCodeException
     */
    public function submitCode(Request $request)
    {
        $this->promo->validatePromoStatus();

        $this->validate($request,
            $this->promo->participationValidationRules()->toArray(),
            $this->promo->participationValidationMessages()->toArray()
        );

        $code = null;
        if( !empty($request->code) ) {
            $code = $this->getRedemptionCode($request->code);
            if(empty($code)) {
                throw new RedemptionCodeException($this->promo);
            }
        }

        $fields = collect($this->promo->participationFieldKeys())->reject(function ($field) {
            return $field === 'code';
        })
            ->map(function ($field) {
                return $field;
            });

        $create = [];
        foreach ( $fields as $field) {
            $create[$field] = $request->get($field);
        }

        $participation = Participation::create($create);
        if( !empty($code) ) {
            $participation->redemptionCode()->associate($code);
            $participation->save();
        }

        return [ 'success' => !empty($participation) ];

    }

    public function getRedemptionCode($code) {
        return RedemptionCode::where('code', $code)->whereDoesntHave('participation')->first();
    }

}
