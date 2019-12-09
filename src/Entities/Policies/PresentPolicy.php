<?php

namespace UnderTheCap\Entities\Policies;

use App\User;
use UnderTheCap\Entities\Present;

class PresentPolicy
{

    public function __construct() {}

    public function viewAny(User $user) {
        return $user->canViewAny(Present::class);
    }

    public function view(User $user, Present $present) {
        return $user->canView( $present );
    }

    public function create(User $user) {
        return $user->canDelete( Present::class );
    }

    public function update(User $user, Present $present) {
        return $user->canUpdate( $present );
    }

    public function delete(User $user, Present $present) {
        return $user->canDelete( $present );
    }

    public function restore(User $user, Present $present) {
        return $user->canRestore( $present );
    }

    public function forceDelete(User $user, Present $present) {
        return $user->canForceDelete( $present );
    }

}