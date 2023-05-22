<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\About\AboutController;

Route::resource('', AboutController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/change-status/{id}', [AboutController::class, 'changeStatus'])->name('Change Status');
