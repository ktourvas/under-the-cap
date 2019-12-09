<?php

namespace UnderTheCap\Entities\Policies;

use App\User;
use UnderTheCap\Entities\Promo;

class PromoPolicy
{

    public function __construct() {}

    public function viewAny(User $user) {
        $user->canViewAny(Promo::class);
    }

    public function view(User $user, Promo $promo) {
        return $user->canView( $promo );
    }

}