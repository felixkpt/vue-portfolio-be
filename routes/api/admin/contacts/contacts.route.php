<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\Contacts\ContactsController;

Route::resource('', ContactsController::class, ['parameters' => ['' => 'id']])->names(['index' => 'list']);
Route::post('/toggle-status/{id}', [ContactsController::class, 'toggleStatus'])->name('Toggle status');
