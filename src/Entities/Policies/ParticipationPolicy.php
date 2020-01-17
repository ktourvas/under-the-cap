<?php

namespace UnderTheCap\Entities\Policies;

use App\User;
use UnderTheCap\Entities\Participation;

class ParticipationPolicy
{

    public function __construct() {}

    public function viewAny(User $user) {
        return $user->canViewAny(Participation::class);
    }

    public function view(User $user, Participation $participation) {
        return $user->canView( $participation );
    }

    public function create(User $user) {
        return $user->canCreate( Participation::class );
    }

    public function update(User $user, Participation $participation) {
        return $user->canUpdate( $participation );
    }

    public function delete(User $user, Participation $participation) {
        return $user->canDelete( $participation );
    }

    public function restore(User $user, Participation $participation) {
        return $user->canRestore( $participation );
    }

    public function forceDelete(User $user, Participation $participation) {
        return $user->canForceDelete( $participation );
    }

}