<?php

namespace UnderTheCap;

use Illuminate\Database\Eloquent\Collection;
use UnderTheCap\Exceptions\PromoConfigurationException;
use UnderTheCap\Exceptions\PromoStatusException;

class Promo {

    protected $info, $participation_fields;

    public function __construct()
    {
        $this->info = config('under-the-cap.current');
        $this->participation_fields = collect(config('under-the-cap.current.participation_fields'));
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
        return array_keys($this->participation_fields->toArray());
    }

    /**
     * The fields of the Participation model that will be used in the relevant query clauses
     * when a search is being performed.
     *
     * @return Collection
     */
    public function ParticipationSearchables() {
        return collect(config('under-the-cap.current.participation_fields'))->map(function($field) {
            return $field;
        })->reject(function ($field) {
            return empty($field['is_searchable']);
        });
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
     * directive an exception is thrown.
     *
     * @return null
     * @throws PromoConfigurationException
     */

    public function validateDrawsConfig() {

        foreach ($this->info['draws']['recursive'] as $draw) {
            array_map(function($f) use ($draw) {
                if(!in_array($f, array_keys($draw))) {
                    throw new PromoConfigurationException('Recursive draws config is not valid');
                }

            }, [ 'title', 'repeat', 'winners_num', 'runnerups_num' ] );
        }

        foreach ($this->info['draws']['adhoc'] as $draw) {
            array_map(function($f) use ($draw) {
                if(!in_array($f, array_keys($draw))) {
                    throw new PromoConfigurationException('Adhoc draws config is not valid');
                }
            }, [ 'title', 'winners_num', 'runnerups_num' ] );
        }

    }

    /**
     * The draws associated with the promo. A validation of the available conf is performed before returning
     * the information array
     *
     * @return array
     * @throws PromoConfigurationException
     */
    public function draws() {

        $this->validateDrawsConfig();

        return collect($this->info['draws']['recursive'] + $this->info['draws']['adhoc'] )
            ->mapWithKeys(function ($type, $key ) {
            return [ $key => $type['title'] ];
        })->toArray();

    }

    /**
     * The types of wins associated with the promo
     *
     * @return array
     */
    public function winTypes() {
        return collect($this->info['draws']['recursive'] + $this->info['draws']['adhoc'])->mapWithKeys(function ($type, $key ) {
            return [ $key => $type['title'] ];
        })->toArray();
    }

    /**
     * The wins types used by the main draw process
     *
     * @return array
     */
//    public function drawableWinTypes() {
//
//        return collect($this->info['win_types'])->map(function($type) {
//            return $type;
//        })->reject(function ($type) {
//            return empty($type['drawable']);
//        });
//
//    }

    public function info() {
        return $this->info;
    }

}
