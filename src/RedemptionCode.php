<?php

namespace UnderTheCap;
use Illuminate\Database\Eloquent\Model;

class RedemptionCode extends Model {

    public function __construct(array $attributes = [])
    {
        $this->table = config('under-the-cap.current.redemption_code_table');
        parent::__construct($attributes);
    }

    /**
     * The Participation associated with the RedemptionCode
     */
    public function participation() {
        return $this->hasOne('UnderTheCap\Participation', 'code_id');
    }

}
