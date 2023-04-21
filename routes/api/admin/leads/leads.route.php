<?php
$controller = \App\Http\Controllers\Api\Admin\Leads\LeadsController::class;
Route::post('/store',[$controller,'storeLead']);
Route::get('/list/self',[$controller,'listSelfLeads']);
Route::get('/list/any',[$controller,'listAnyLeads']);
Route::get('/get/any/{id}',[$controller,'getAnyLead']);
Route::get('/get/self/{id}',[$controller,'getLead']);
