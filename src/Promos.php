<?php

namespace UnderTheCap;

class Promos {

    private $promos, $current;

    public function __construct()
    {
        $this->promos = collect([]);

        if( config('under-the-cap') !== null ) {
            foreach ( config('under-the-cap') as $key => $promo ) {
                $this->promos->put($key, new Promo($promo));
            }
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

    /**
     * Returns the promos collection item that corresponds to the $this->current name.
     * If none is set then the first found is returned.
     *
     * @return mixed
     */
    public function current() {

        if( $this->promos->has( $this->current ) ) {
            return $this->promos->get($this->current);
        }
        return $this->promos->first();
    }

}