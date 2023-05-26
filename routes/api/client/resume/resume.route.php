<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\Resume\ResumeController;

Route::get('/', [ResumeController::class, 'index']);
Route::post('/download', [ResumeController::class, 'download']);
