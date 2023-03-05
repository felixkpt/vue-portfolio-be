<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Leads\LeadsController;

Route::get('/', [LeadsController::class, 'index']);
Route::post('/', [LeadsController::class, 'storeLead']);
Route::get('/list-options', [LeadsController::class, 'listSelectOptions']);
Route::get('/list', [LeadsController::class, 'listLeads']);
Route::post('/toggle-status/{id}', [LeadsController::class, 'toggleLeadStatus']);
Route::delete('/delete/{id}', [LeadsController::class, 'destroyLead']);
