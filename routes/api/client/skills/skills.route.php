<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\Skills\SkillsController;

Route::get('/', [SkillsController::class, 'index']);
Route::get('/{id}', [SkillsController::class, 'show']);
