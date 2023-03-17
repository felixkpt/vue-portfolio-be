<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Tasks\TasksController;

Route::get('/', [TasksController::class, 'index']);
Route::post('/', [TasksController::class, 'storeTask']);
Route::get('/list-options', [TasksController::class, 'listSelectOptions']);
Route::get('/list', [TasksController::class, 'listTasks']);
Route::post('/toggle-status/{id}', [TasksController::class, 'toggleTaskStatus']);
Route::delete('/delete/{id}', [TasksController::class, 'destroyTask']);
