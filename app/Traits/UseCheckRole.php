<?php


namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * this trait is used internally for checking whether user has role permissions
*/
trait UseCheckRole
{
    /**
     * checking if user has any roles
     * @param array $roles
     * @return bool
     * @krismonsemanas
    */
    public function checkRole(...$roles) : bool
    {
        $user = User::find(Auth::id());

        return $user->hasRole($roles);
    }

    /**
     * checking if user has role super admin
     * @return bool
     * @krismonsemanas
    */
    public function isSuperAdmin() : bool
    {
        return $this->checkRole('super-admin');
    }

    /**
     * checking if user has any permission
     * @param array $permissions
     * @return bool
     * @krismonsemanas
    */
    public function checkPermission(...$permisssions) : bool
    {
        $user = User::find(Auth::id());

        return $user->hasAnyPermission(...$permisssions);
    }
}
