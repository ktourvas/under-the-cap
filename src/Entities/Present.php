<?php

namespace UnderTheCap\Entities;
use Illuminate\Database\Eloquent\Model;

class Present extends Model {

    protected $fillable = [ 'date', 'draw_id', 'daily_give', 'total_give', 'total_given' ];

    protected $hidden = [ 'draw_id', 'daily_give', 'daily_given', 'track', 'total_give', 'total_given' ];

    public function __construct(array $attributes = [])
    {
        $this->promo = \App::make('UnderTheCap\Entities\Promos')->current();

        $this->table = $this->promo->info()['participation_presents_table'] ?? 'presents';

        parent::__construct($attributes);
    }

    public function variants() {
        return $this->hasMany('UnderTheCap\Entities\PresentVariant', 'present_id');
    }

}
