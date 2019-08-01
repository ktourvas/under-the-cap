<?php

namespace UnderTheCap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Win extends Model {

    protected $types, $promo, $draw;

    protected $fillable = [ 'type_id', 'participation_id', 'bumped', 'associated_date', 'confirmed', 'runnerup', 'present_id' ];

    protected $hidden = [ 'runnerup', 'bumped', 'associated_date', 'confirmed' ];

    protected $appends = [ 'type_name' ];

    public function __construct( $attributes = [] )
    {
        $this->promo = App::make('UnderTheCap\Promos')->current();

        $this->table = $this->promo->info()['wins_table'];
//        $this->table = config('under-the-cap.current.wins_table');

//        $this->promo = App::make('UnderTheCap\Promo');

        parent::__construct($attributes);
    }

    /**
     * @deprecated
     * @deprecated the new relation currently named winpresent will be replacing this
     *
     * The gift associated with the win
     */
    public function present() {
        return $this->belongsTo('UnderTheCap\Entities\Present', 'present_id');
    }

    /**
     * The gift associated with the win
     */
    public function winpresent() {
        return $this->hasOne('UnderTheCap\Entities\WinPresent', 'win_id', 'id');
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
        return !empty( $this->promo->draw($this->type_id) ) ? $this->promo->draw($this->type_id)['title'] : 'not set';
    }

    /**
     * The Win associated type name
     */
    public function getTypeNameAttribute() {
        return $this->runnerup == 0 ? 'Winner' : 'Runnerup';
    }

}
