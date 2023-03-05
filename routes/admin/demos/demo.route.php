<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Demos\DemosController;

Route::get('/', [DemosController::class, 'index']);
Route::post('/', [DemosController::class, 'storeDemo']);
Route::get('/list-options', [DemosController::class, 'listSelectOptions']);
Route::get('/list', [DemosController::class, 'listDemos']);
Route::post('/toggle-status/{id}', [DemosController::class, 'toggleDemoStatus']);
Route::delete('/delete/{id}', [DemosController::class, 'destroyDemo']);
