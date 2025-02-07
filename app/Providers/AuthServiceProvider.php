<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Disposition;
use App\Policies\RolePolicy;
use App\Policies\EmployeePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\PermissionPolicy;
use Spatie\Permission\Models\Role;
use App\Policies\DispositionPolicy;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
       Permission::class => PermissionPolicy::class,
       Role::class => RolePolicy::class,
       Employee::class => EmployeePolicy::class,
       Department::class => DepartmentPolicy::class,
       Disposition::class => DispositionPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        //
    }
}
