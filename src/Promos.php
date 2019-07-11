<?php

namespace UnderTheCap;

class Promos {

    private $promos, $current;

    public function __construct()
    {
        foreach ( config('under-the-cap') as $key => $promo ) {
            $this->promos[$key] = new Promo($promo);
        }
    }

    public function promo($key) {
        return $this->promos[$key];
    }

    public function setCurrent($slug) {
        $this->current = $slug;
    }

    public function current() {
        return $this->promos[$this->current];
    }

}