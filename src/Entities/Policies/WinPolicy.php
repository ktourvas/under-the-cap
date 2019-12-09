<?php

namespace UnderTheCap\Entities\Policies;

use App\User;
use laravel\contacts\Contact;
use UnderTheCap\Entities\Win;

class WinPolicy
{

    public function __construct() {}

    public function viewAny(User $user) {
        return $user->canViewAny(Win::class);
    }

    public function view(User $user, Win $win) {
        return $user->canView($win);
    }

    public function create(User $user) {
        return $user->canCreate(Win::class);
    }

    public function update(User $user, Win $win) {
        return $user->canUpdate($win);
    }

    public function delete(User $user, Win $win) {
        return $user->canDelete($win);
    }

    public function restore(User $user, Win $win) {
        return $user->canRestore($win);
    }

    public function forceDelete(User $user, Win $win) {
        return $user->canForceDelete($win);
    }

}