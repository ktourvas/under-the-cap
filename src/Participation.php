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

}
