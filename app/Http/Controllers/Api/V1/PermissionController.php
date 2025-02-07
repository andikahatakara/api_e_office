<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseFormatter;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\V1\StorePermissionRequest;
use App\Http\Requests\V1\UpdatePermissionRequest;
use App\Traits\UseCheckRole;

class PermissionController extends Controller
{
    use UseCheckRole;
    /**
     * Display a listing of the resource.
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function index() : JsonResponse
    {
        $this->authorize('viewAny', Permission::class);



        if($this->checkPermission(['permissions.index']) && !$this->isSuperAdmin()) {
            $permissions = Permission::whereNot('name', 'like', '%permissions%')->get();
        } else {
            $permissions = Permission::all();
        }

        $permissions->map(function($permission) {
            $permission['actions'] = [
                [
                    'type' => 'update',
                    'action' => $permission->id,
                    'can' => 'permissions.update',
                    'useModal' => true
                ],
                [
                    'type' => 'delete',
                    'action' => 'permissions/'.$permission->id,
                    'can' => 'permissions.destroy',
                    'useModal' => true
                ],
            ];

            return $permission;
        });

        return ApiResponseFormatter::success($permissions, 'Berhasil mengambil data hak akses');
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\V1\StorePermissionRequest $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function store(StorePermissionRequest $request) : JsonResponse
    {
        $this->authorize('create', Permission::class);

        $created = DB::transaction(function () use ($request) {
            $input = $request->only('title', 'name');
            $input['guard_name'] = 'web';

            $permission = Permission::make($input);

            $permission->saveOrFail();

            $role = Role::findById(1, 'web');

            if($role) {
                $role->givePermissionTo($permission);
            }
            return $permission;
        });

        return $created ?
            ApiResponseFormatter::success($created, 'Berhasil menambahkan hak akses') :
            ApiResponseFormatter::error('Gagal menambahkan data');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param \App\Http\Requests\V1\UpdatePermissionRequest $request
     * @param \Spatie\Permission\Models\Permission $permission
     * @krismonsemanas
     */
    public function update(UpdatePermissionRequest $request, Permission $permission) : JsonResponse
    {
        $this->authorize('update', $permission);

        $updated = DB::transaction(function() use($request, $permission) {
            $input = $request->only('title', 'name');

            return $permission->update($input);
        });

        return $updated ?
            ApiResponseFormatter::success($updated, 'Berhasil mengupdate hak akses') :
            ApiResponseFormatter::error('Gagal mengupdate data');
    }

    /**
     * Remove the specified resource from storage.
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function destroy(Permission $permission) : JsonResponse
    {
        $this->authorize('delete', $permission);
        $deleted = DB::transaction(function() use($permission) {
            return $permission->delete();
        });

        return $deleted ?
            ApiResponseFormatter::success($deleted, 'Berhasil menghapus hak akses') :
            ApiResponseFormatter::error('Gagal menghapus data');
    }
}
