<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\UpdatePasswordRequest;
use App\Http\Requests\Api\V1\Auth\UpdateProfileRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Handle user login and issue a mobile API token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->input('login');
        $user = User::where('email', $login)->orWhere('username', $login)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return $this->errorResponse('Email, username, atau kata sandi tidak cocok.', 401);
        }

        $deviceName = $request->input('device_name', 'Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'Login berhasil.');
    }

    /**
     * Handle logout and revoke the current device token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil.');
    }

    /**
     * Get the authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user()), 'Profil pengguna berhasil dimuat.');
    }

    /**
     * Update the authenticated user profile.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $this->successResponse(new UserResource($user), 'Profil berhasil diperbarui.');
    }

    /**
     * Update the authenticated user password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return $this->successResponse(null, 'Kata sandi berhasil diperbarui.');
    }
}
