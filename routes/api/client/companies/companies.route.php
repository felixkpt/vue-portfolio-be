<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\Companies\CompaniesController;

Route::get('/', [CompaniesController::class, 'index']);
Route::get('/{id}', [CompaniesController::class, 'show']);
