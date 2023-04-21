<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Settings\Permissiongroups\PermissionGroupsController;

Route::get('/', [PermissiongroupsController::class, 'index']);
Route::post('/', [PermissiongroupsController::class, 'storePermissionGroup']);
Route::put('/{id}', [PermissiongroupsController::class, 'storePermissionGroup']);
Route::get('/list-options', [PermissiongroupsController::class, 'listSelectOptions']);
Route::get('/list', [PermissiongroupsController::class, 'listPermissionGroups']);
Route::post('/toggle-status/{id}', [PermissiongroupsController::class, 'togglePermissionGroupStatus']);
Route::delete('/delete/{id}', [PermissiongroupsController::class, 'destroyPermissionGroup']);
