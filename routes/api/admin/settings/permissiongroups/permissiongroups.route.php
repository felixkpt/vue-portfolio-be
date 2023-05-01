<?php

use App\Http\Controllers\Admin\Settings\Permissiongroups\PermissionGroupsController;

$controller = PermissionGroupsController::class;

Route::get('/', [$controller, 'list'])->name('list');
Route::post('/', [$controller, 'store'])->name('store');
Route::put('/{id}', [$controller, 'store'])->name('update');
Route::delete('/{id}', [$controller, 'destroy'])->name('delete');
Route::get('/routes', [$controller, 'listRoutes']);
Route::get('/roles', [$controller, 'listRoles']);
