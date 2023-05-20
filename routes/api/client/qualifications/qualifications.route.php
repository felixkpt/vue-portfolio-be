<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\Qualifications\QualificationsController;

Route::get('/', [QualificationsController::class, 'index']);
Route::get('/{id}', [QualificationsController::class, 'show']);
