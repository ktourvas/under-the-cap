<?php

namespace UnderTheCap\Entities;
use Illuminate\Database\Eloquent\Model;

class Present extends Model {

    protected $fillable = [ 'date', 'draw_id', 'daily_give', 'total_give', 'total_given' ];

    public function __construct(array $attributes = [])
    {
        $this->promo = \App::make('UnderTheCap\Promos')->current();

        $this->table = $this->promo->info()['participation_presents_table'];

        parent::__construct($attributes);
    }

}
