<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\GroupMemberController;
use App\Http\Controllers\Api\GroupRoleController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuditLogController;
use App\Models\GroupMember;
use App\Models\GroupRole;
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
        Route::get('/workspace', [GroupController::class,'individualWorkspace']);
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
        Route::post('/invites/{invitation}', [GroupMemberController::class,'acceptInvite']);

        Route::prefix('{group}')->group(function () {

            Route::patch('/', [GroupController::class, 'update']);
            Route::delete('/', [GroupController::class, 'destroy']);
            Route::post('/leave', [GroupMemberController::class, 'leave']);
            Route::get('/permissions', [GroupMemberController::class, 'myPermissions']);
            Route::get('/auditlog', [AuditLogController::class, 'index']);

            Route::prefix('roles')->scopeBindings()->group(function () {

                Route::get('/', [GroupRoleController::class, 'index']);
                Route::post('/', [GroupRoleController::class, 'store']);

                Route::prefix('{groupRole}')->group(function () {
                    Route::get('/', [GroupRoleController::class, 'show']);
                    Route::patch('/', [GroupRoleController::class, 'update']);
                    Route::delete('/', [GroupRoleController::class,'destroy']);
                });
            });

            Route::prefix('members')->scopeBindings()->group(function () {
                Route::get('/', [GroupMemberController::class, 'index']);
                Route::post('/invite', [GroupMemberController::class, 'invite']);

                Route::prefix('{member}')->group(function () {
                    Route::get('/', [GroupMemberController::class, 'show']);
                    Route::delete('/', [GroupMemberController::class, 'kickMember']);
                    Route::patch('/', [GroupMemberController::class, 'syncRoles']);
                });
            });
        });
    });
});