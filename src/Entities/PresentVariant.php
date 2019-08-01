<?php

namespace UnderTheCap\Entities;
use Illuminate\Database\Eloquent\Model;

class PresentVariant extends Model {

    protected $fillable = [ 'remaining' ];

    protected $hidden = [ 'remaining' ];

    public function __construct(array $attributes = [])
    {
        $this->promo = \App::make('UnderTheCap\Promos')->current();

        $this->table = $this->promo->info()['participation_present_variants_table'] ?? 'present_variants';

        parent::__construct($attributes);
    }

    public function present() {
        return $this->belongsTo('UnderTheCap\Entities\Present', 'present_id');
    }

}
