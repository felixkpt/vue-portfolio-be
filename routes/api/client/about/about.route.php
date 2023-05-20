<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\About\AboutController;

Route::get('/', [AboutController::class, 'index']);
Route::get('/{id}', [AboutController::class, 'show']);
