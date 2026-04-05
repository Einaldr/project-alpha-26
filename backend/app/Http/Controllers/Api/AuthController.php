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
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;

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

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = PasswordFacade::broker()->sendResetLink($request->only('email'));

        return $status === PasswordFacade::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required||confirmed|min:8',
        ]);

        $status = PasswordFacade::broker()->reset($request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));

                $user->tokens()->delete();

                $user->save();
            });
        
        return $status === PasswordFacade::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
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
