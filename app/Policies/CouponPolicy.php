<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Coupon;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Coupon');
    }

    public function view(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('View:Coupon');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Coupon');
    }

    public function update(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('Update:Coupon');
    }

    public function delete(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('Delete:Coupon');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Coupon');
    }

}