<?php

namespace UnderTheCap;
use Illuminate\Database\Eloquent\Model;

class Win extends Model {

    protected $types;

    public function __construct(array $attributes = [])
    {
        $this->types = config('under-the-cap.win_types');
        parent::__construct($attributes);
    }

    /**
     * The Participation associated with the Win
     */
    public function participation() {
        return $this->belongsTo('UnderTheCap\Participation', 'participation_id');
    }

    /**
     * The Win associated type name
     */
    public function type() {
        return !empty($this->types[$this->type_id]) ? $this->types[$this->type_id] : 'n/a';
    }

}
