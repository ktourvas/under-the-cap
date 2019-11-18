<?php

namespace UnderTheCap\Policies;

use App\User;
use UnderTheCap\Promo;

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