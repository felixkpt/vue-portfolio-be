<?php

use App\Cih\Http\Controllers\Auth\AuthController;

use Illuminate\Support\Facades\Route;

$apiAuthController = AuthController::class;
Route::post('auth/login', [$apiAuthController, 'login']);
Route::get('/auth/token/{token}', [$apiAuthController, 'getUserByToken']);
Route::post('auth/reset', [$apiAuthController, 'resetPassword']);
Route::post('auth/forgot', [$apiAuthController, 'forgotPassword']);
Route::post('auth/register', [$apiAuthController, 'register']);
Route::post('auth/logout', [$apiAuthController, 'logoutUser']);
