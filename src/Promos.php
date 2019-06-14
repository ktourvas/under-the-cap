<?php

namespace UnderTheCap;

class Promos {

    private $promos;

    public function __construct()
    {
        foreach ( config('under-the-cap') as $key => $promo ) {
            $this->promos[$key] = new Promo($promo);
        }
    }

    public function promo($key) {
        return $this->promos[$key];
    }

}