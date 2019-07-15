<?php

namespace UnderTheCap\Policies;

use App\User;
use UnderTheCap\Participation;

class ParticipationPolicy
{

    public function __construct() {}

    public function viewAny(User $user) {
        return $user->permissions()
                ->where('policy', 'Participation')
                ->where('viewany', 1)
                ->count() === 1;
    }

    public function create(User $user)
    {

        return $user->permissions()
                ->where('policy', 'Participation')
                ->where('view', 1)
                ->count() === 1;
    }

    public function view(User $user, Participation $participation)
    {
        return $user
                ->permissions()
                ->where('policy', 'Participation')
                ->where(function($q) use ($participation) {
                    $q->where('view', 1)
                        ->orWhereHas('records', function($q) use ($participation) {
                            $q->where('record_id', $participation->id)->where('view', 1);
                        });
                })
                ->count() > 0;
    }

    public function update(User $user, Participation $participation)
    {
        return $user
                ->permissions()
                ->where('policy', 'Participation')
                ->where(function($q) use ($participation) {
                    $q->where('update', 1)
                        ->orWhereHas('records', function($q) use ($participation) {
                            $q->where('record_id', $participation->id)->where('update', 1);
                        });
                })
                ->count() > 0;
    }

    public function delete(User $user, Participation $participation)
    {
        return $user
                ->permissions()
                ->where('policy', 'Participation')
                ->where(function($q) use ($participation) {
                    $q->where('delete', 1)
                        ->orWhereHas('records', function($q) use ($participation) {
                            $q->where('record_id', $participation->id)->where('delete', 1);
                    });
                })
            ->count() > 0;
    }

    public function restore(User $user, Participation $participation)
    {
        return $user
                ->permissions()
                ->where('policy', 'Participation')
                ->where(function($q) use ($participation) {
                    $q->where('delete', 1)
                        ->orWhereHas('records', function($q) use ($participation) {
                            $q->where('record_id', $participation->id)->where('delete', 1);
                        });
                })
                ->count() > 0;
    }

    public function forceDelete(User $user, Participation $participation)
    {
        return $user
                ->permissions()
                ->where('policy', 'Participation')
                ->where(function($q) use ($participation) {
                    $q->where('delete', 1)
                        ->orWhereHas('records', function($q) use ($participation) {
                            $q->where('record_id', $participation->id)->where('delete', 1);
                        });
                })
                ->count() > 0;
    }

}