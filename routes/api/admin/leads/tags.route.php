<?php
$controller = \App\Http\Controllers\Api\Admin\Leads\LeadTagsController::class;
Route::post('/store',[$controller,'tag']);
Route::post('/update-tag/{id}',[$controller,'updateTagStatus']);
Route::get('/list/self',[$controller,'listSelfLeadTags']);
Route::get('/list/any',[$controller,'listAnyTags']);

Route::post('/lead-tag/store',[$controller,'storeLeadTag']);
Route::get('/lead-tag/list/any',[$controller,'listAnyLeadTags']);
