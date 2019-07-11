<?php

namespace UnderTheCap;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use UnderTheCap\Exceptions\PromoConfigurationException;
use UnderTheCap\Exceptions\PromoStatusException;
use Carbon\Carbon;

class Promo {

    protected $info, $participation_fields, $period;

    public function __construct($info = null)
    {
        $this->info = $info;
        if( empty($this->info) ) {
            $this->info = config('under-the-cap.current');
        }

        $this->period = CarbonPeriod::create($this->info['start_date'], $this->info['end_date']);

        $this->info['start_date'] = strtotime($this->info['start_date']);
        $this->info['end_date'] = strtotime($this->info['end_date']);

        $this->participation_fields = collect($this->info['participation_fields']);
//        $this->participation_fields = collect(config('under-the-cap.current.participation_fields'));
    }

    /**
     * The title of the promo
     *
     * @return string
     */
    public function title() {
        return $this->info['name'];
    }

    /**
     * The slug identifier of the promo
     *
     * @return string
     */
    public function slug() {
        return $this->info['slug'];
    }

    /**
     * The status of the current promotion. Returns pending (p), running (r) or completed (e)
     *
     * @return string
     */
    public function status() {
        return time() >= $this->info['start_date'] ? time() >= $this->info['end_date'] ? 'e' : 'r' : 'p';
    }

    /**
     * @return CarbonPeriod
     */
    public function period() {
        return $this->period;
    }

    /**
     * Returns the integer number of the current date in respect to the promo interval
     * @return int
     */
    public function dayNumber($zerobased = false) {
        return $this->period->getStartDate()->setHour('0')->diffInDays(Carbon::now()) + ($zerobased ? 0 : 1);
    }

    /**
     * The whole of the Participation model fields information.
     *
     * @return array
     */
    public function participationFields() {
        return $this->participation_fields->toArray();
    }

    /**
     * The total of the key names of the Participation model fields. To be used for model creation.
     *
     * @return array
     */
    public function participationFieldKeys() {
        return array_keys( $this->participation_fields->toArray() );
    }

    /**
     * The fields of the Participation model that will be used in the relevant query clauses
     * when a search is being performed.
     *
     * @return Collection
     */
    public function ParticipationSearchables() {

        return collect($this->info['under-the-cap.current.participation_fields'])->filter(function ($field, $key) {
            return !empty($field['is_searchable']);
        });

//        return collect(config('under-the-cap.current.participation_fields'))->filter(function ($field, $key) {
//            return !empty($field['is_searchable']);
//        });

    }

    /**
     * The validation rules to be used on Participation submissions
     *
     * @return Collection
     */
    public function participationValidationRules() {
        return $this->participation_fields->mapWithKeys(function ($field, $key ) {
            return [ $key => $field['rules'] ];
        });
    }

    /**
     * The validation error messages to be used on Participation submissions
     *
     * @return Collection
     */
    public function participationValidationMessages() {
        return $this->participation_fields->mapWithKeys(function ($field) {
            return $field['messages'];
        });
    }

    /**
     * Validate the status pf the current promo against the running status and throw an exception if outside the
     * running period bounds. To be used on Participation submissions
     *
     * @throws PromoStatusException
     */
    public function validatePromoStatus() {
        switch ($this->status()) {
            case 'e':
            case 'p':
                throw new PromoStatusException($this);
                break;
        }
    }

    /**
     * Validates the available draws configuration info against the minimum required item fields. In case of a missing
     * info bit, an exception is thrown.
     *
     * @return null
     * @throws PromoConfigurationException
     */

    public function validateDrawsConfig() {

        //TODO: refactor fields array to compare against depending on type

        if(!empty($this->info['draws'])) {

            collect($this->info['draws'])

                /**
                 * deprecated: to be removed in later versions
                 */
                ->reject(function($draw, $key) {
                    return in_array($key , [
                        'adhoc',
                        'instant',
                        'recursive'
                    ]);
                })

                ->map(function($draw, $key) {

                    array_map(function($f) use ($draw) {
                        if(!in_array($f, array_keys($draw))) {
                            throw new PromoConfigurationException('Draws config is not valid');
                        }

                    }, [
                        'title',
                        'type',
//                        'winners_num'
                    ]


                    );

                });

        }

    }

    /**
     * The draws associated with the promo. A validation of the available conf is performed before returning
     * the information array
     *
     * @return \Illuminate\Support\Collection
     * @throws PromoConfigurationException
     */
    public function draws() {

        $this->validateDrawsConfig();

        return collect(
            (!empty($this->info['draws']) ? $this->info['draws'] : [])
        );

    }

    /**
     * The draws associated with the promo which are considered adhoc. Adhoc draws are presented as options for
     * manually requesting a draw by admins.
     *
     * @return \Illuminate\Support\Collection
     * @throws PromoConfigurationException
     */
    public function adhocDraws() {

        $this->validateDrawsConfig();

        return collect($this->info['draws'])
            ->reject(function($draw) {
                return empty($draw['type']) || $draw['type'] !== 'adhoc';
            });

    }

    /**
     *
     * The draws associated with the promo which are assigned instantly. If a draw is of type instant, it will trigger
     * an instant win request after a participation submission.
     *
     * @return \Illuminate\Support\Collection
     * @throws PromoConfigurationException
     */
    public function instantDraws() {

        $this->validateDrawsConfig();

        return collect($this->info['draws'])
            ->reject(function($draw) {
                return empty($draw['type']) || $draw['type'] !== 'instant';
            });

    }

    /**
     *
     * Get information of a draw, by id.
     *
     * @return \Illuminate\Support\Collection
     * @throws PromoConfigurationException
     */
    public function draw($id) {

        $this->validateDrawsConfig();

        return collect( $this->info['draws'] )
            ->filter(function ( $draw, $key ) use ($id) {
                return $key == $id;
            })->first();
    }

    public function info() {
        return $this->info;
    }

}
