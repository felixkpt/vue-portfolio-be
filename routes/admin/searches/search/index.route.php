<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Searches\Search\SearchController;

Route::get('/{id}', [SearchController::class, 'index']);
Route::post('/{id}', [SearchController::class, 'storeSearch']);
Route::post('/toggle-status/{id}', [SearchController::class, 'toggleSearchStatus']);
Route::delete('/delete/{id}', [SearchController::class, 'destroySearch']);
