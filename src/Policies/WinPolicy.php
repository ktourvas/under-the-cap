<?php

namespace UnderTheCap\Policies;

use App\User;
use laravel\contacts\Contact;

class WinPolicy
{

    public function __construct() {}

    public function viewAny(User $user) {
        return $user->permissions()
                ->where('policy', 'Win')
                ->where('viewany', 1)
                ->count() === 1;
    }

    public function view(User $user, Win $win)
    {
        return false;
    }

    public function create(User $user, Win $win)
    {
        return false;
    }

    public function update(User $user, Win $win)
    {
        return false;
    }

    public function delete(User $user, Win $win)
    {
        return false;
    }

    public function restore(User $user, Win $win)
    {
        return false;
    }

    public function forceDelete(User $user, Win $win)
    {
        return false;
    }

}