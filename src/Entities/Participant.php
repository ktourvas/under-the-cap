<?php

namespace UnderTheCap\Entities;

trait Participant {

    public function participations() {
        return $this->hasMany('UnderTheCap\Entities\Participation', 'user_id');
    }

}