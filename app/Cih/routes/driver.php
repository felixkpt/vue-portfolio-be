<?php

use Illuminate\Support\Facades\Route;

$middleWares = ['web', 'sh_auth'];


Route::group(['middleware' => $middleWares, 'prefix' => ''], function () {
    Route::get('/homes', [App\Http\Controllers\HomeController::class, 'index']);
});
