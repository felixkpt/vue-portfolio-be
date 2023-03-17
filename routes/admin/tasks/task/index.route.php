<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Tasks\Task\TaskController;

Route::get('/{id}', [TaskController::class, 'index']);
Route::post('/{id}', [TaskController::class, 'storeTask']);
Route::post('/toggle-status/{id}', [TaskController::class, 'toggleTaskStatus']);
Route::delete('/delete/{id}', [TaskController::class, 'destroyTask']);
