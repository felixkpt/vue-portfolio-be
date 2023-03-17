<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Teams\TeamsController;

Route::get('/', [TeamsController::class, 'index']);
Route::post('/', [TeamsController::class, 'storeTeam']);
Route::get('/list-options', [TeamsController::class, 'listSelectOptions']);
Route::get('/list', [TeamsController::class, 'listTeams']);
Route::post('/toggle-status/{id}', [TeamsController::class, 'toggleTeamStatus']);
Route::delete('/delete/{id}', [TeamsController::class, 'destroyTeam']);
