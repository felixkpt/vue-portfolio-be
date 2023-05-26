<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\SkillsCategories\SkillsCategoriesController;

Route::get('/', [SkillsCategoriesController::class, 'index']);
Route::get('/{id}', [SkillsCategoriesController::class, 'show']);
