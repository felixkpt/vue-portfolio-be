<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\SkillsCategories\SkillsCategoriesController;

Route::resource('', SkillsCategoriesController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/change-status/{id}', [SkillsCategoriesController::class, 'changeStatus'])->name('Change Status');
