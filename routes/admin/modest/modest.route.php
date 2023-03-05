<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Modest\ModestsController;

Route::get('/', [ModestsController::class, 'index']);
Route::post('/', [ModestsController::class, 'storeModest']);
Route::get('/list-options', [ModestsController::class, 'listSelectOptions']);
Route::get('/list', [ModestsController::class, 'listModests']);
Route::post('/toggle-status/{id}', [ModestsController::class, 'toggleModestStatus']);
Route::delete('/delete/{id}', [ModestsController::class, 'destroyModest']);
