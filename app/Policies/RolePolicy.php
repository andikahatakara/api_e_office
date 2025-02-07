<?php

namespace App\Policies;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Perform pre-authorization checks.
    */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.index');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        if(!$user->hasRole('super-admin') && $role->id === 1) {
            return false;
        }
        return $user->can('roles.show');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('roles.store');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        if(!$user->hasRole('super-admin') && $role->id <= 3) {
            return false;
        }
        return $user->can('roles.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        if(!$user->hasRole('super-admin') && $role->id <= 3) {
            return false;
        }
        return $user->can('roles.delete');
    }

    /**
     * Determine whether the user can sync role and permissions
     */
    public function syncPermission(User $user): bool
    {
        return $user->can('roles.sync.permission');
    }

    /**
     * Determine whether the user can sync role and user
     */
    public function syncUser(User $user): bool
    {
        return $user->can('roles.sync.user');
    }

}
