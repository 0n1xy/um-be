<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

    
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::group([
    'middleware' => ['jwt.custom:admin'],
    'prefix' => 'admin'
], function () {
    Route::get('/users', [UserController::class, 'getAllUserData']);
    Route::get('/users/{id}', [UserController::class, 'getUserById']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'delete']);
});

Route::group([
    'middleware' => 'jwt.custom',
    'prefix' => 'auth'
], function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [UserController::class, 'updateMe']);
    Route::post('/logout', [UserController::class, 'logout']);
});
