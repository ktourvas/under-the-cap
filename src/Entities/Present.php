<?php

namespace UnderTheCap\Entities;
use Illuminate\Database\Eloquent\Model;

class Present extends Model {

    protected $fillable = [ 'date', 'total' ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('under-the-cap.current.participation_presents_table');

        parent::__construct($attributes);
    }

}
