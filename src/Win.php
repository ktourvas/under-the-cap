<?php

namespace UnderTheCap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Win extends Model {

    protected $types, $promo, $draws;

    protected $fillable = [ 'type_id', 'participation_id', 'bumped', 'associated_date', 'confirmed', 'runnerup' ];

    protected $appends = [ 'type_name' ];

    public function __construct( $attributes = [] )
    {
        $this->table = config('under-the-cap.current.wins_table');

        $this->promo = App::make('UnderTheCap\Promo');
        $this->draws = $this->promo->draws();

//        $this->types = config('under-the-cap.current.win_types');
        parent::__construct($attributes);
    }

    /**
     * The Participation associated with the Win
     */
    public function participation() {
        return $this->belongsTo('UnderTheCap\Participation', 'participation_id');
    }

    /**
     * The Win associated draw name
     */
    public function getDrawNameAttribute() {
        return !empty($this->draws[$this->type_id]) ? $this->draws[$this->type_id] : 'n/a';
    }

    /**
     * The Win associated type name
     */
    public function getTypeNameAttribute() {
        return $this->runnerup == 0 ? 'Winner' : 'Runnerup';
    }

}
