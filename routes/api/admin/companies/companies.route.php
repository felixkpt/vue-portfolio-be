<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Companies\CompaniesController;

Route::resource('', CompaniesController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/toggle-status/{id}', [CompaniesController::class, 'toggleStatus'])->name('Toggle status');
