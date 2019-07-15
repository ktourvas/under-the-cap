<?php

namespace UnderTheCap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Participation extends Model {

    protected $promo, $attributted_fields;

    public function __construct(array $attributes = [])
    {
        $this->promo = App::make('UnderTheCap\Promos')->current();
        $this->table = $this->promo->info()['participation_table'];
        $this->fillable = array_merge(
            array_keys($this->promo->info()['participation_fields']),
            [ 'code_id' ]
        );
        $this->attributted_fields = collect($this->promo->info()['participation_fields'])
            ->filter(function ($field) {
                return !empty($field['is_id']);
            });
        parent::__construct($attributes);
    }

    /**
     * The RedemptionCode associated with the Participation
     */
    public function redemptionCode() {
        return $this->belongsTo('UnderTheCap\RedemptionCode', 'code_id');
    }

    /**
     * The Wins associated with the Participation.
     */
    public function win() {
        return $this->hasMany('UnderTheCap\Win', 'participation_id');
    }

    public function getDynamicField($field) {

        return !empty($this->attributted_fields[$field]) ?

            !empty($this->attributted_fields[$field]['is_id']['titles'][$this->getAttribute($field)]) ?

                $this->attributted_fields[$field]['is_id']['titles'][$this->getAttribute($field)] :

                $this->getAttribute($field)

            :

            $this->getAttribute($field);

    }


    public function getpromoattribute() {
        return $this->promo;
    }
}
