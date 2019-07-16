<?php

namespace UnderTheCap;

class Promos {

    private $promos, $current;

    public function __construct()
    {
        $this->promos = collect([]);
        foreach ( config('under-the-cap') as $key => $promo ) {
            $this->promos->put($key, new Promo($promo));
        }
    }

    public function promo($key) {

        return $this->promos->get($key);

    }

    public function setCurrent($slug) {

        if( $this->promos->has( $slug ) ) {
            $this->current = $slug;
        }

        return $slug == $this->current;
    }

    public function current() {
        return $this->promos->get($this->current);
    }

}