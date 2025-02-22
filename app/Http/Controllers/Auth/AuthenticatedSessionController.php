<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response | JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return ApiResponseFormatter::success(true, 'Berhasil Login');
        }


        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response | JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return ApiResponseFormatter::success(true, 'Berhasil Logout');
        }

        return response()->noContent();
    }
}
