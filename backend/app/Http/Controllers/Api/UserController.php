<?php

namespace App\Http\Controllers\Api;

use App\Enum\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a list of users.
     */
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $perPage = $request->integer('per_page', 15);

        $query = User::query()->where('account_status', AccountStatus::ACTIVE);

        if ($search = $request->search) {
            $query->whereRaw('name % ?', [$search])
                  ->orderByRaw('similarity(name, ?) DESC', [$search]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return UserResource::collection(
            $query->paginate($perPage)->appends($request->query())
        );
    }

    /**
     * Register a user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $currentTosVersion = config('settings.tos_version');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'tos_accepted_at' => now(),
            'tos_version' => $currentTosVersion,
        ]);

        $token = $user->createToken('frontend_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
            'token' => $token
            ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user): UserResource
    {
        $isMe = auth('sanctum')->check() && auth('sanctum')->id() === $user->id;

        if ($user->account_status !== AccountStatus::ACTIVE && ! $isMe) {
            abort(404, 'User not found');
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
