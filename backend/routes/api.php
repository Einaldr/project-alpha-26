<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\patch;

// --- Authentication endpoints ---
Route::post('/auth/register', [UserController::class, 'store']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/password/reset');

// --- User endpoints ---
Route::get('/users', [UserController::class, 'index']);
Route::get('/user/{user}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // --- Secure Auth-related endpoints
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/auth/password/change', [AuthController::class, 'changePassword']);
    
    // --- User related secure endpoints
    Route::get('/users/me', [ProfileController::class, 'me']);
    Route::patch('/users/me', [UserController::class, 'update']);
    Route::delete('/users/me', [UserController::class, 'destroy']);
    Route::delete('/users/me/pernament', [UserController::class, 'forceDestroy']);

});
