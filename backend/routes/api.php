<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// --- Authentication endpoints ---
Route::prefix('auth') -> group(function() {
    Route::post('/register', [UserController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);

    // --- Password reset endpoints ---
    Route::prefix('password')->group(function () {
        Route::post('/password/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
        Route::post('/password/reset-password', [AuthController::class, 'resetPassword']);
    });
});

// --- User endpoints ---
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);

// --- Group endpoints ---
Route::prefix('groups')->group(function () {
    Route::get('/', [GroupController::class, 'index']);
    Route::get('/{group_id}');
});

Route::middleware('auth:sanctum')->group(function () {
    // --- Secure Auth-related endpoints
    Route::prefix('auth')->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/password/change', [AuthController::class, 'changePassword']);
    });

    // --- User related secure endpoints
    Route::prefix('users')->group(function () {
        Route::get('/me', [ProfileController::class, 'me']);
        Route::patch('/me', [UserController::class, 'update']);
        Route::delete('/me', [UserController::class, 'destroy']);
        Route::delete('/me/pernament', [UserController::class, 'forceDestroy']);
    });

    // --- Secure group endpoints
    Route::prefix('groups')->group(function () {
        Route::post('/', [GroupController::class, 'store']);
        Route::patch('/{group_id}');
        Route::delete('/{group_id}');
    });
});
