<?php

namespace UnderTheCap;
use Illuminate\Database\Eloquent\Model;

class RedemptionCode extends Model {

    public function __construct(array $attributes = [])
    {
        $this->promo = \App::make('UnderTheCap\Entities\Promos')->current();

        $this->table = $this->promo->info()['redemption_code_table'];

        parent::__construct($attributes);
    }

    /**
     * The Participation associated with the RedemptionCode
     */
    public function participation() {
        return $this->hasOne('UnderTheCap\Entities\Participation', 'code_id');
    }

}
