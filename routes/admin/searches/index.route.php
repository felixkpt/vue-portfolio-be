<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Searches\SearchesController;

Route::get('/', [SearchesController::class, 'index']);
Route::post('/', [SearchesController::class, 'storeSearch']);
Route::get('/list-options', [SearchesController::class, 'listSelectOptions']);
Route::get('/list', [SearchesController::class, 'listSearches']);
Route::post('/toggle-status/{id}', [SearchesController::class, 'toggleSearchStatus']);
Route::delete('/delete/{id}', [SearchesController::class, 'destroySearch']);
