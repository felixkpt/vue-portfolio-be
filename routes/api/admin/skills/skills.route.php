<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Skills\SkillsController;

Route::resource('', SkillsController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/change-status/{id}', [SkillsController::class, 'changeStatus'])->name('Change Status');
