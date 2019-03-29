<?php

namespace UnderTheCap;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model {

    public function __construct(array $attributes = [])
    {
        $this->table = config('under-the-cap.participation_table');
        $this->fillable = config('under-the-cap.participation_fields');
        parent::__construct($attributes);
    }

    /**
     * The RedemptionCode associated with the Participation
     */
    public function redemptionCode() {
        return $this->belongsTo('UnderTheCap\RedemptionCode', 'code_id');
    }

    /**
     * The Wins associated with the Participation.
     */
    public function win() {
        return $this->hasMany('UnderTheCap\Win');
    }
}
