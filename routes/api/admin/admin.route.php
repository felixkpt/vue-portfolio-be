<?php

use App\Http\Controllers\Admin\AdminController;

$controller = AdminController::class;
Route::get('/', [$controller, 'index'])->name('Dashboard');
Route::get('/testing2', [$controller, 'index']);
