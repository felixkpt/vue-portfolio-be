<?php

use App\Http\Controllers\Admin\Settings\Permissiongroups\PermissionGroupsController;

$controller = PermissionGroupsController::class;
Route::get('/routes', [$controller, 'listRoutes']);
Route::get('/roles', [$controller, 'listRoles']);
Route::get('/list', [$controller, 'listPermissionGroups']);
Route::post('/store/{id?}', [$controller, 'storePermissionGroup']);
Route::get('/permission_group/{id}', [$controller, 'getPermissionGroup']);
Route::post('/permissions/{id}', [$controller, 'updatePermissions']);
Route::delete('/delete/{id}', [$controller, 'destroyPermissionGroup']);
Route::get('/all-permissions', [$controller, 'allPermissions']);
