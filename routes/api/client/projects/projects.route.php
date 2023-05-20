<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\Projects\ProjectsController;

Route::get('/', [ProjectsController::class, 'index']);
Route::get('/{id}', [ProjectsController::class, 'show']);
