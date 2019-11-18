<?php

namespace UnderTheCap\Policies;

use App\User;
use UnderTheCap\Participation;
use UnderTheCap\RedemptionCode;

class RedemptionCodePolicy
{

    public function __construct() {}

    public function viewAny(User $user) {
        return $user->canViewAny(RedemptionCode::class);
    }

    public function create(User $user) {
        return $user->canCreate(RedemptionCode::class);
    }

    public function view(User $user, RedemptionCode $code) {
        return $user->canView($code);
    }

    public function update(User $user, RedemptionCode $code) {
        return $user->canUpdate($code);
    }

    public function delete(User $user, RedemptionCode $code) {
        return $user->canDelete($code);
    }

    public function restore(User $user, RedemptionCode $code) {
        return $user->canRestore($code);
    }

    public function forceDelete(User $user, RedemptionCode $code) {
        return $user->canForceDelete($code);
    }

}