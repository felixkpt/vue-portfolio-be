<?php
$controller = \App\Http\Controllers\Api\Admin\Leads\LeadCategoriesController::class;
Route::post('/store',[$controller,'storeLeadCategory']);
Route::get('/list/self',[$controller,'listSelfLeadCategories']);
Route::get('/list/any',[$controller,'listAnyLeadCategories']);
Route::get('/get/any/{id}',[$controller,'getAnyLeadCategory']);
Route::get('/get/self/{id}',[$controller,'getLeadCategory']);
Route::post('/update-status/{id}',[$controller,'updateCategory']);
