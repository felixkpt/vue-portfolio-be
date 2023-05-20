<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Projects\ProjectsController;

Route::resource('', ProjectsController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/toggle-status/{id}', [ProjectsController::class, 'toggleStatus'])->name('Toggle status');
