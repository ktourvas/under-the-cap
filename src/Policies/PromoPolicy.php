<?php

namespace UnderTheCap\Policies;

use App\User;
use UnderTheCap\Promo;

class PromoPolicy
{

    public function __construct() {}

    public function viewAny(User $user) {
        dd('viewany');
//        return $user->permissions()
//                ->where('policy', 'Promo')
//                ->where('viewany', 1)
//                ->count() === 1;
        return true;
    }

    public function view(User $user, Promo $promo)
    {

//        $promo->info()->get('id');
//        $promoid = $promo->info()['id'];
//        $promoinfo = $promo->info();
//        dd($promoinfo);
//        $promoid = $promoinfo['id'];
//        dd( $promo->info()['id'] );
//        dd('view');
//        $info = $promo->info();
//        dd($info['id']);

//        if( !empty($promo->info()['id']) && $promo->info()['id'] == 3 ) {
//            dd($user
//                ->permissions()
//                ->where('policy', 'Promo')
//
//                ->where(function($q) use ($promo) {
//
//                    $q->where('view', 1)
//                        ->orWhereHas('records', function($q) use ($promo) {
//                            $q
//                                ->where( 'record_id', $promo->info()['id'] ?? 0 )
//                                ->where('view', 1);
//                        });
//                })->count());
//        }

        return $user
                ->permissions()
                ->where('policy', 'Promo')
                ->where(function($q) use ($promo) {
                    $q->where('view', 1)
                        ->orWhereHas('records', function($q) use ($promo) {
                            $q
                                ->where( 'record_id', $promo->info()['id'] ?? 0 )
                                ->where('view', 1);
                        });
                })
                ->count() > 0;
    }

//    public function create(User $user)
//    {
//
//        return $user->permissions()
//                ->where('policy', 'Participation')
//                ->where('view', 1)
//                ->count() === 1;
//    }
//
//
//    public function update(User $user, Participation $participation)
//    {
//        return $user
//                ->permissions()
//                ->where('policy', 'Participation')
//                ->where(function($q) use ($participation) {
//                    $q->where('update', 1)
//                        ->orWhereHas('records', function($q) use ($participation) {
//                            $q->where('record_id', $participation->id)->where('update', 1);
//                        });
//                })
//                ->count() > 0;
//    }
//
//    public function delete(User $user, Participation $participation)
//    {
//        return $user
//                ->permissions()
//                ->where('policy', 'Participation')
//                ->where(function($q) use ($participation) {
//                    $q->where('delete', 1)
//                        ->orWhereHas('records', function($q) use ($participation) {
//                            $q->where('record_id', $participation->id)->where('delete', 1);
//                        });
//                })
//                ->count() > 0;
//    }
//
//    public function restore(User $user, Participation $participation)
//    {
//        return $user
//                ->permissions()
//                ->where('policy', 'Participation')
//                ->where(function($q) use ($participation) {
//                    $q->where('delete', 1)
//                        ->orWhereHas('records', function($q) use ($participation) {
//                            $q->where('record_id', $participation->id)->where('delete', 1);
//                        });
//                })
//                ->count() > 0;
//    }
//
//    public function forceDelete(User $user, Participation $participation)
//    {
//        return $user
//                ->permissions()
//                ->where('policy', 'Participation')
//                ->where(function($q) use ($participation) {
//                    $q->where('delete', 1)
//                        ->orWhereHas('records', function($q) use ($participation) {
//                            $q->where('record_id', $participation->id)->where('delete', 1);
//                        });
//                })
//                ->count() > 0;
//    }

}