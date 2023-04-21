<?php

use App\Http\Controllers\Admin\Settings\Permissiongroups\PermissionGroupsController;

$controller = PermissionGroupsController::class;
Route::get('/routes', [$controller, 'listRoutes']);
Route::get('/roles', [$controller, 'listRoles']);
Route::get('/list', [$controller, 'listPermissionGroups']);
Route::post('/{id?}', [$controller, 'storePermissionGroup']);
Route::get('/permission_group/{id}', [$controller, 'getPermissionGroup']);
Route::post('/permissions/{id}', [$controller, 'updatePermissions']);
Route::post('/permission_group/delete/{id}', [$controller, 'deletePermissionGroup']);
Route::get('/all-permissions', [$controller, 'allPermissions']);
