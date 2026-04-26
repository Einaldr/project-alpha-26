<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     *  GET /me
     * 
     *  Get the logged-in user's profile
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     *  PATCH /me
     * 
     *  Update user's account name
     */
    public function update(Request $request): UserResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:4'],
        ]);

        $user = $request->user();

        $user->update([
            'name' => $validated['name'],
        ]);

        return new UserResource($user);
    }

    public function updateEmail(Request $request)
    {
        //
    }

    /**
     *  DELETE /v1/me
     * 
     *  Soft delete (deactivate) a user
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'User deactivated successfully'
        ], 200);
    }

    /**
     *  DELETE /v1/me/pernament
     * 
     *  Pernamently (GDPR) erase the user
     */
    public function forceDestroy(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();

        $user->forceDestroy();

        return response()->json([
            'message' => 'Your account and all associated content has been pernamently erased.',
        ]);
    }
}
