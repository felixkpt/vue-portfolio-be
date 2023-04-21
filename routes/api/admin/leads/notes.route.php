<?php
$controller = \App\Http\Controllers\Api\Admin\Leads\LeadNotesController::class;
Route::post('/store',[$controller,'storeLeadNote']);
Route::get('/list/self',[$controller,'listSelfLeadNotes']);
Route::get('/list/any/{id}',[$controller,'listAnyLeadNotes']);
Route::get('/get/any/{id}',[$controller,'getAnyLeadNote']);
Route::get('/get/self/{id}',[$controller,'getLeadNote']);
