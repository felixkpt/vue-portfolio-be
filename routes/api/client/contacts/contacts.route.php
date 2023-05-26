<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\Contacts\ContactsController;

Route::get('/', [ContactsController::class, 'index']);
Route::get('/', [ContactsController::class, 'index']);
Route::post('/send-message', [ContactsController::class, 'sendMessage']);
