<?php

use App\Http\Controllers\Api\Admin\Posts\PostsController;
use Illuminate\Support\Facades\Route;

$controller = PostsController::class;

Route::get('/', [$controller, 'index'])->name('list');
Route::post('/', [$controller, 'store'])->name('store');
Route::put('/create', [$controller, 'update'])->name('create');
Route::get('/{id}', [$controller, 'show'])->name('show');
Route::put('/{id}', [$controller, 'update'])->name('update');
Route::delete('/{id}', [$controller, 'delete'])->name('delete');