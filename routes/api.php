<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\DispositionController;
use App\Http\Controllers\Api\V1\IncomingLetterController;
use App\Http\Controllers\Api\V1\OutgoingLetterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->prefix(config('app.version'))->name('api.')->group(function() {

    // dashboard
    Route::get('dashboard/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::get('dashboard/overview', [DashboardController::class, 'overview'])->name('overview');

    // user profile route
    Route::get('users/profile', [ProfileController::class, 'profile'])->name('profile.show');
    Route::put('users/profile', [ProfileController::class, 'updatePersonalInformation'])->name('profile.update.personal.information');
    Route::put('users/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::put('users/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');

    // employee
    Route::get('employees/departments', [EmployeeController::class, 'departments']);
    Route::get('employees/head', [EmployeeController::class, 'head']);
    Route::apiResource('employees', EmployeeController::class);

    // incoming letter
    Route::post('incoming_letters/{incoming_letter}/disposition', [IncomingLetterController::class, 'disposition'])->name('incoming_letters.disposition');
    Route::get('incoming_letters/department-lists', [IncomingLetterController::class, 'departments'])->name('incoming_letters.department.lists');
    Route::apiResource('incoming_letters', IncomingLetterController::class);
    Route::apiResource('outgoing_letters', OutgoingLetterController::class);

    // dispositions
    Route::apiResource('dispositions', DispositionController::class)->only('show');

    // department
    Route::apiResource('departments', DepartmentController::class);

    // role
    Route::put('roles/{role}/permissions/{permission}', [RoleController::class, 'syncPermission'])->name('roles.sync.permission');
    Route::put('roles/{role}/users/{user}', [RoleController::class, 'syncUser'])->name('roles.sync.user');
    Route::apiResource('roles', RoleController::class);

    // permission
    Route::apiResource('permissions', PermissionController::class);

    Route::delete('auth/logout', [AuthController::class, 'logout']);
});

Route::prefix(config('app.version'))->group(function() {
    Route::post('auth/login', [AuthController::class, 'login']);
});
