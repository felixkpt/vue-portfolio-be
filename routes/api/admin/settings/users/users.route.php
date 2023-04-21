<?php

use Illuminate\Support\Facades\Route;

$controller = \App\Http\Controllers\Api\Admin\Settings\Users\UsersController::class;
Route::post('/', [$controller, 'storeUser']);
Route::post('/store', [$controller, 'storeUser']);
Route::get('/list/{role?}', [$controller, 'listUsers']);
Route::get('/user/{id}', [$controller, 'getUser']);
Route::post('/user/status/{id}', [$controller, 'updateStatus']);
Route::get('/user/{id}/logs', [$controller, 'listUserLogs']);
Route::post('/user/profile', [$controller, 'updateProfile']);
Route::post('/user/delete/{id}', [$controller, 'deleteUser']);
Route::post('/update-user', [$controller, 'updateUser']);
Route::post('/update-password/{id}', [$controller, 'UpdatePassword']);
