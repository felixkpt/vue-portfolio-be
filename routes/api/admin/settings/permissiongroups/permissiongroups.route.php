<?php

use App\Http\Controllers\Api\Admin\Settings\Permissiongroups\PermissionGroupsController;

$controller = PermissionGroupsController::class;

Route::get('/', [$controller, 'list']);
Route::post('/', [$controller, 'store']);
Route::put('/{id}', [$controller, 'store'])->name('update');
Route::delete('/{id}', [$controller, 'destroy'])->name('delete');
Route::get('/routes', [$controller, 'listRoutes']);
Route::get('/roles', [$controller, 'listRoles']);
