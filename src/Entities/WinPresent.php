<?php

namespace UnderTheCap\Entities;

use Illuminate\Database\Eloquent\Model;

class WinPresent extends Model {

    protected $promo;

    protected $fillable = [ 'present_id', 'variant_id' ];

    public function __construct(array $attributes = [])
    {
        $this->promo = \App::make('UnderTheCap\Entities\Promos')->current();

        $this->table = !empty($this->promo->info()['participation_win_presents_table']) ? $this->promo->info()['participation_win_presents_table'] : 'win_presents';

        parent::__construct($attributes);
    }

    public function win() {
        return $this->belongsTo('UnderTheCap\Entities\Win', 'win_id');
    }

    public function present() {
        return $this->belongsTo('UnderTheCap\Entities\Present', 'present_id');
    }

    public function variant() {
        return $this->belongsTo('UnderTheCap\Entities\PresentVariant', 'variant_id');
    }

}
