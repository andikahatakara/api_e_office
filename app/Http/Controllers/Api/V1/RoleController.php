<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponseFormatter;
use App\Http\Requests\V1\StoreRoleRequest;
use App\Http\Requests\V1\UpdateRoleRequest;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function index() : JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $user = User::find(Auth::id());


        if($user->can('roles.index') && !$user->hasRole('super-admin')) {
            $roles = Role::whereNotIn('id', [1])->get();
        } else {
            $roles = Role::all();
        }

        $roles->map(function($role) use($user) {
            $role['actions'] = [
                [
                    'type' => 'update',
                    'action' => $role->id,
                    'can' => 'roles.update',
                    'useModal' => true,
                    'disabled' => $role->id <= 3 && !$user->hasRole('super-admin')  ? true :  false,
                ],
                [
                    'type' => 'delete',
                    'action' => 'roles/'.$role->id,
                    'can' => 'roles.destroy',
                    'useModal' => true,
                    'disabled' => $role->id <= 3 && !$user->hasRole('super-admin')  ? true :  false,
                ],
            ];
        });

        return ApiResponseFormatter::success($roles, 'Berhasil mengambil semua data peran');
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\V1\StoreRoleRequest $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function store(StoreRoleRequest $request) : JsonResponse
    {
        $this->authorize('create', Role::class);

        $created = DB::transaction(function() use($request) {
            $input = $request->only('title', 'name');
            $input['guard_name'] = 'web';
            return Role::create($input);
        });

        return $created ?
            ApiResponseFormatter::success($created, 'Berhasil menambahkan data peran') :
            ApiResponseFormatter::error('Gagal menambahkan data peran');
    }

    /**
     * Display the specified resource.
     * @param \Spatie\Permission\Models\Role $role
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function show(Role $role) : JsonResponse
    {
        $this->authorize('view', $role);

        $roleHasPermissions = collect([]);

        $role->permissions->map(function($permission) use($roleHasPermissions) {
            $roleHasPermissions[$permission->name] = true;
        });
        $role->users;

        $data = [
            'role' => $role->makeHidden('permissions'),
            'hasPermissions' => $roleHasPermissions
        ];

        return ApiResponseFormatter::success($data, 'Berhasil mendapatkan data role');
    }

    /**
     * Update the specified resource in storage.
     * @param \App\Http\Requests\V1\UpdateRoleRequest $request
     * @param \Spatie\Permission\Models\Role $role
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function update(UpdateRoleRequest $request, Role $role) : JsonResponse
    {
        $this->authorize('update', $role);
        $updated = DB::transaction(function() use($request, $role) {
            $input = $request->only('title', 'name');
            return $role->update($input);
        });
        return $updated ?
            ApiResponseFormatter::success($updated, 'Berhasil merubah data peran') :
            ApiResponseFormatter::error('Gagal merubah data peran');
    }

    /**
     * Remove the specified resource from storage.
     * @param \Spatie\Permission\Models\Role $role
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function destroy(Role $role) : JsonResponse
    {
        $this->authorize('delete', $role);

        $deleted = DB::transaction(function() use($role) {
            return $role->delete();
        });

        return $deleted ?
            ApiResponseFormatter::success($deleted, 'Berhasil menghapus data peran') :
            ApiResponseFormatter::error('Gagal menghapus data peran');
    }

    /**
     * syncronize role and permissions
     * @param \Spatie\Permission\Models\Role $role;
     * @param \Spatie\Permission\Models\Permission $permission;
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function syncPermission(Role $role, Permission $permission) : JsonResponse
    {
        $this->authorize('syncPermission', $role);

        $sync = DB::transaction(function() use($role, $permission) {
            if($role->hasPermissionTo($permission)) {
                return $role->revokePermissionTo($permission);
            }

            return $role->givePermissionTo($permission);

        });

        return $sync ?
            ApiResponseFormatter::success(true, 'Berhasil sinkronisasi peran dan hak akses') :
            ApiResponseFormatter::success('Gagal sinkronisasi peran dan hak akses');
    }

    /**
     * syncronize role and user
     * @param \Spatie\Permission\Models\Role $role;
     * @param \App\Models\User $user;
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function syncUser(Role $role, User $user)
    {
        $this->authorize('syncUser', $role);

        $sync = DB::transaction(function () use($role, $user) {
            // check user has role
            if($user->hasRole($role)) {
                return $user->removeRole($role);
            }

            return $user->assignRole($role);
        });

        return $sync ?
            ApiResponseFormatter::success($sync, 'Berhasil sinkron peran pengguna') :
            ApiResponseFormatter::error('Gagal sinkron peran pengguna');
    }
}
