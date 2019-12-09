<?php

namespace UnderTheCap\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use UnderTheCap\Entities\WinPresent;
use UnderTheCap\Events\ParticipationSubmitted;
use UnderTheCap\Exceptions\RedemptionCodeException;
use UnderTheCap\Invokable\InstantWinsManager;
use UnderTheCap\Entities\Participation;
use UnderTheCap\Entities\Promo;
use UnderTheCap\RedemptionCode;
use UnderTheCap\Win;

class ParticipationsController extends Controller {

    protected $promo;

    public function __construct(Request $request)
    {
        if(!empty($request->utc_env)) {
            \App::make('UnderTheCap\Entities\Promos')->setCurrent($request->utc_env);
            $this->promo = \App::make('UnderTheCap\Entities\Promos')->current();
        }

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
        $code = null;
        if( $submissionType == 'redemption' && !empty($request->code) ) {
            $code = $this->getRedemptionCode($request->code);
            if(empty($code)) {
                throw new RedemptionCodeException($this->promo);
            }
        }

        $participation = $this->createParticipation($request, $code);

        $participation = $this->playInstant($participation);

        // All went well, emit the ParticipationSubmitted event
        if(!empty($participation)) {
            event( new ParticipationSubmitted($participation) );
        }

        return [
            'success' => !empty( $participation ),
            'participation' => !empty( $participation ) ? $participation : null
        ];

    }

    /**
     * Retrieve a redemption code by code
     * @param $code
     * @return mixed
     */
    public function getRedemptionCode(String $code) {
        return RedemptionCode::where('code', $code)->whereDoesntHave('participation')->first();
    }

    /**
     * Run the instant win invokable and attach wins, if any, to the participation
     * @param Participation $participation
     * @return Participation
     * @throws \UnderTheCap\Exceptions\PromoConfigurationException
     */
    public function playInstant(Participation $participation) {

        if( $this->promo->instantDraws()->count() > 0 ) {

            $instant = new InstantWinsManager();

            foreach( $this->promo->instantDraws() as $id => $info ) {

                if( !empty($info['restrict_wins_by']) ) {
                    if(
                        Participation::whereHas('win', function($q) use ($id, $info) {
                            $q->where('type_id', $id);
                        })
                            ->where(function($q) use ($participation, $info) {
                                if(is_array($info['restrict_wins_by'])) {
                                    foreach ($info['restrict_wins_by'] as $field ) {
                                        $q->where($field, $participation[$field]);
                                    }
                                } else {
                                    $q->where($info['restrict_wins_by'], $participation[$info['restrict_wins_by']]);
                                }
                            })
                            ->count() != 0)
                    {
                        continue;
                    }
                }

                $win = $instant($id, $info, $participation);

                if( $win !== false) {

                    $pwin = $participation->win()->create([
                        'type_id' => $id,
                        'present_id' => $win['id'],
                        'confirmed' => (!empty($info['auto_approved']) &&  $info['auto_approved'] === true) ? 1 : 0
                    ]);

                    $winpresent = $pwin->winpresent()->create([
                        'present_id' => $win['id']
                    ]);

                }

            }

            $participation->load('win.winpresent.present');

            if( $participation->win()->exists() ) {

                $participation->win[0]->winpresent->present->load(['variants' => function($q) {
                    $q->where( 'remaining', '>', 0);
                }]);

                if( count($participation->win[0]->winpresent->present->variants) == 1 ) {

                    $participation->win[0]->winpresent->update([
                        'variant_id' => $participation->win[0]->winpresent->present->variants[0]->id
                    ]);

                    $participation->win[0]->winpresent->present->variants[0]->update([
                        'remaining' =>  \DB::raw(' remaining - 1 ')
                    ]);

                }

            }

        }
        return $participation;
    }

    /**
     * Create and return a new Participation.
     *
     * @param $request
     * @param null $code
     * @return Participation
     */
    private function createParticipation($request, $code = null) {

        // Get the participation create associative array
        $create = $this->participationCreateArray($request);

        // Create a new participation associated with the user if there is one
        $participation = $request->user('api') === null ?
            Participation::create($create) :
            $request->user('api')->participations()->create($create);

        // If a code has been validated, assign it to the participation
        if( !empty($code) ) {
            $participation->redemptionCode()->associate($code);
            $participation->save();
        }

        return $participation;
    }

    /**
     * Create the associative array to be used for participation creation
     * @param $request
     * @return array
     */
    private function participationCreateArray($request) {
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
        return $create;
    }


    public function truncate() {
        if(!\App::environment('production')) {
            WinPresent::truncate();
            Win::truncate();
            Participation::truncate();
        }
    }

}
