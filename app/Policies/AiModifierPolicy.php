<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AiModifier;
use Illuminate\Auth\Access\HandlesAuthorization;

class AiModifierPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AiModifier');
    }

    public function view(AuthUser $authUser, AiModifier $aiModifier): bool
    {
        return $authUser->can('View:AiModifier');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AiModifier');
    }

    public function update(AuthUser $authUser, AiModifier $aiModifier): bool
    {
        return $authUser->can('Update:AiModifier');
    }

    public function delete(AuthUser $authUser, AiModifier $aiModifier): bool
    {
        return $authUser->can('Delete:AiModifier');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AiModifier');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AiModifier');
    }

}