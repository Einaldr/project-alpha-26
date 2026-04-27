<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/**
 * ================
 * STATIC ROUTES
 * ================
 */

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

// --- Group endpoints ---
Route::prefix('groups')->group(function () {
    Route::get('/', [GroupController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    // --- Secure Auth-related endpoints
    Route::prefix('auth')->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/password/change', [AuthController::class, 'changePassword']);
    });

    // --- User related secure endpoints
    Route::prefix('me')->group(function () {
        Route::get('/', [ProfileController::class, 'me']);
        Route::patch('/', [ProfileController::class, 'update']);
        Route::delete('/', [ProfileController::class, 'destroy']);
        Route::delete('/pernament', [ProfileController::class, 'forceDestroy']);

        Route::get('/groups', [GroupController::class, 'myGroups']);
    });

    // --- Secure group endpoints
    Route::prefix('groups')->group(function () {
        Route::post('/', [GroupController::class, 'store']);
    });
});

/**
 * ==========================
 * DYNAMIC/WILDCARD ROUTES
 * ==========================
 */

// User non secure routes
Route::get('/users/{user}', [UserController::class, 'show']);

// Group non secure routes
Route::get('/groups/{group}', [GroupController::class, 'show']);

// Secure routes
Route::middleware('auth:sanctum')->group(function () {

    // Secure group routes
    Route::prefix('groups')->group(function () {
        Route::patch('/{group}', [GroupController::class, 'update']);
        Route::delete('/{group}', [GroupController::class, 'destroy']);
    });
});