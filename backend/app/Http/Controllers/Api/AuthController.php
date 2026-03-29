<?php

namespace App\Http\Controllers\Api;

use App\Enum\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * 
     *  Login request
     * 
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        if ($user->account_status !== AccountStatus::ACTIVE) {
            throw ValidationException::withMessages([
                'email' => ['This account has been ' . strtolower($user->account_status?->label()) . '.'],
            ]);
        }

        $token = $user->createToken('frontend_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }


    /**
     * 
     * Change password
     * 
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults()],
        ]);

        $request->user()->tokens()->delete();
        $token = $request->user()->createToken('frontend_token')->plainTextToken;

        return response()->json([
            'message' => 'Password changed successfully.',
            'token' => $token,
        ]);
    }

    /**
     * 
     * Logout from the current device
     * 
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * 
     * Logout from all devices
     * 
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out of all devices successfully'
        ], 200);
    }
}
