<?php

namespace UnderTheCap;
use Illuminate\Database\Eloquent\Model;

class Win extends Model {

    protected $types;

    protected $fillable = [ 'type_id', 'participation_id', 'bumped', 'associated_date', 'confirmed' ];

    protected $appends = [ 'type_name' ];

    public function __construct($attributes = [])
    {
        $this->table = config('under-the-cap.current.wins_table');
        $this->types = config('under-the-cap.current.win_types');
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
    public function getTypeNameAttribute() {
        return !empty($this->types[$this->type_id]) ? $this->types[$this->type_id]['title'] : 'n/a';
    }

}
