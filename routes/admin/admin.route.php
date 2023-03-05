<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', [AdminController::class, 'index']);
Route::post('/', [AdminController::class, 'storeAdmin']);
Route::get('/list-options', [AdminController::class, 'listSelectOptions']);
Route::get('/list', [AdminController::class, 'listAdmins']);
Route::post('/toggle-status/{id}', [AdminController::class, 'toggleAdminStatus']);
Route::delete('/delete/{id}', [AdminController::class, 'destroyAdmin']);
