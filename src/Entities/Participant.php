<?php

namespace UnderTheCap\Entities;

trait Participant {

    public function participations() {
        return $this->hasMany('UnderTheCap\Participation', 'user_id');
    }

}