<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Projects\ProjectsController;

Route::resource('', ProjectsController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/change-status/{id}', [ProjectsController::class, 'changeStatus'])->name('Change Status');
