<?php

namespace UnderTheCap\Entities;
use Illuminate\Database\Eloquent\Model;

class InstantsStatus extends Model {

    protected $table = 'utc_instants_status';
    protected $fillable = [ 'slug', 'wins', 'plays' ];

}
