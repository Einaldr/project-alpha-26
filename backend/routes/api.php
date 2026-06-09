<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\GroupMemberController;
use App\Http\Controllers\Api\GroupRoleController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectFileController;
use App\Http\Controllers\Api\ProjectMemberController;
use App\Http\Controllers\Api\ProjectSecretsController;
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
            Route::get('/auditlogs', [AuditLogController::class, 'index']);

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

            Route::prefix('projects')->scopeBindings()->group(function () {
                Route::get('/', [ProjectController::class, 'index']);  // List projects in this group
                Route::post('/', [ProjectController::class, 'store']); // Create project in this group

                Route::prefix('{project}')->group(function () {
                    Route::get('/', [ProjectController::class, 'show']);
                    Route::patch('/', [ProjectController::class, 'update']);
                    Route::delete('/', [ProjectController::class, 'destroy']);

                    Route::prefix('secrets')->group(function () {
                        Route::get('/', [ProjectSecretsController::class, 'show']);
                        Route::put('/', [ProjectSecretsController::class, 'update']);
                        Route::delete('/', [ProjectSecretsController::class, 'destroy']);
                    });

                    Route::prefix('members')->group(function () {
                        Route::get('/', [ProjectMemberController::class, 'index']);
                        Route::post('/', [ProjectMemberController::class, 'store']); // Direct add from Org members

                        Route::prefix('{projectMember}')->group(function () {
                            Route::patch('/', [ProjectMemberController::class, 'update']);
                            Route::delete('/', [ProjectMemberController::class, 'destroy']);
                        });
                    });

                    Route::prefix('files')->group(function () {
                        Route::get('/', [ProjectFileController::class, 'index']);    // Lazy-load folder tree
                        Route::get('/show', [ProjectFileController::class, 'show']); // Read raw file content
                    });

                    Route::get('/branches', [ProjectFileController::class, 'branches']);
                    Route::post('/pull', [ProjectFileController::class, 'pull']);
                    Route::post('/checkout', [ProjectFileController::class, 'checkout']);
                    Route::get('/permissions', [ProjectController::class, 'myPermissions']);
                });
            });
        });
    });
});