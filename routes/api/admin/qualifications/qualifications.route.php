<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Qualifications\QualificationsController;

Route::resource('', QualificationsController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/toggle-status/{id}', [QualificationsController::class, 'toggleStatus'])->name('Toggle status');
