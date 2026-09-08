<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Property;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Property');
    }

    public function view(AuthUser $authUser, Property $property): bool
    {
        return $authUser->can('View:Property');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Property');
    }

    public function update(AuthUser $authUser, Property $property): bool
    {
        return $authUser->can('Update:Property');
    }

    public function delete(AuthUser $authUser, Property $property): bool
    {
        return $authUser->can('Delete:Property');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Property');
    }

    public function replicate(AuthUser $authUser, Property $property): bool
    {
        return $authUser->can('Replicate:Property');
    }

}