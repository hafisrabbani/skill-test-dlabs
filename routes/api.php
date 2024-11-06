<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;

Route::group([
    'prefix' => 'auth',
    'as' => 'auth.',
], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware([
    AuthMiddleware::class
])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('posts', PostController::class);
});

