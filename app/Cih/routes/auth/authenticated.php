<?php

use App\Cih\Http\Controllers\Auth\AuthController;

use Illuminate\Support\Facades\Route;

$apiAuthController = AuthController::class;
Route::post('auth/user', [$apiAuthController, 'updateProfile']);
Route::get('auth/user', [$apiAuthController, 'getUser']);