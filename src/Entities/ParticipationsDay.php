<?php

namespace UnderTheCap\Entities;
use Illuminate\Database\Eloquent\Model;

class ParticipationsDay extends Model {

    protected $promo;

    protected $fillable = [ 'date', 'total' ];

    public function __construct(array $attributes = [])
    {

        $this->promo = \App::make('UnderTheCap\Entities\Promos')->current();

        $this->table = $this->promo->info()['participation_stats_table'];

        parent::__construct($attributes);
    }

}
