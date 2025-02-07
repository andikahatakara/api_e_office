<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ApiResponseFormatter;
use App\Http\Requests\V1\UpdateAvatarRequest;
use App\Http\Requests\V1\UpdatePasswordRequest;
use App\Http\Requests\V1\UpdatePersonalInformationRequest;
use App\Traits\UseFileUpload;

class ProfileController extends Controller
{
    use UseFileUpload;

    /**
     * define default directory for user profile
     * @var string $directory
    */
    private $directory = 'avatars';

    /**
     * Display a user profile
     * @return \App\Helpers\ApiResponseFormatter
    */
    public function profile() : JsonResponse
    {
        $user = User::find(Auth::id());

        $permissions = collect([]);

        $user->getPermissionsViaRoles()->map(function($permission) use($permissions) {
            return $permissions[$permission->name] = true;
        });

       $user['permissions'] = $permissions;

       $user->load('employee','employee.employeeable');

        return $user ?
            ApiResponseFormatter::success($user, 'Berhasil mengambil profile') :
            ApiResponseFormatter::error('Gagal mendapatkan profile');
    }

    /**
     * Update personal information
     * @param \App\Http\Requests\V1\UpdatePersonalInformationRequest $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function updatePersonalInformation(UpdatePersonalInformationRequest $request) : JsonResponse
    {
        $user = User::find(Auth::id());

        $updated = DB::transaction(function () use($request, $user) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->nip = $request->nip;
            $user->save();

            return $user;
        });

        return $updated ?
            ApiResponseFormatter::success($updated, 'Berhasil mengambil profile') :
            ApiResponseFormatter::error('Gagal mendapatkan profile');
    }

    /**
     * Update user password
     * @param \App\Http\Requests\V1\UpdatePasswordRequest $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function password(UpdatePasswordRequest $request) : JsonResponse
    {
        $updated = DB::transaction(function() use($request) {
            $user = User::find(Auth::id());
            $password = Hash::make($request->password);

            return $user->update(['password' => $password]);
        });

        if($updated) {
            Auth::guard('web')->logout();
            return ApiResponseFormatter::success($updated, 'Berhasil mengganti password, Silahkan login kembali');
        }

        return ApiResponseFormatter::error('Gagal mengupdate password');
    }

    /**
     * Change avatar user
     * @param \App\Http\Requests\V1\UpdateAvatarRequest $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function avatar(UpdateAvatarRequest $request) : JsonResponse
    {
        $updated = DB::transaction(function() use($request) {
            $user = User::find(Auth::id());
            $avatar = $request->file('avatar');

            $path = $this->syncFile($avatar, $user->avatar, $this->directory);

            return $user->update(['avatar' => $path]);
        });

        return $updated ?
            ApiResponseFormatter::success($updated, 'Berhasil mengupdate foto'):
            ApiResponseFormatter::error('Gagal mengupdate foto');
    }
}
