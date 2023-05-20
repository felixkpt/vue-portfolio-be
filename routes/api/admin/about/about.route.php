<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\About\AboutController;

Route::resource('', AboutController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/toggle-status/{id}', [AboutController::class, 'toggleStatus'])->name('Toggle status');
