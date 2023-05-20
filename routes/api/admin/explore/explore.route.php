<?php

use App\Http\Controllers\Api\Admin\Explore\ExploreController;
use Illuminate\Support\Facades\Route;

$controller = ExploreController::class;

Route::get('/', [$controller, 'index'])->name('list');
Route::post('/', [$controller, 'store'])->name('store');
Route::put('/create', [$controller, 'update'])->name('create');
Route::get('/{id}', [$controller, 'show'])->name('show');
Route::put('/{id}', [$controller, 'update'])->name('update');
Route::delete('/{id}', [$controller, 'delete'])->name('delete');