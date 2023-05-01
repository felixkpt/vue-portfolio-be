<?php

use App\Http\Controllers\Admin\AdminController;

$controller = AdminController::class;
Route::get('/testing', [$controller, 'index']);
Route::get('/testing2', [$controller, 'index']);
