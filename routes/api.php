<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SchoolClassController;

Route::get('/home/products' , HomePageController::class);
Route::get('/products/{product:slug}' , ProductController::class);
Route::get('/search/filters' , [SearchController::class , 'getFilters']);
Route::get('/search' , [SearchController::class , 'search']);
Route::get('/ads' , AdController::class);
Route::get('/school/classes/{schoolClass}' , SchoolClassController::class);
Route::get('/contacts' , ContactController::class);
Route::get('/activities' , ActivityController::class);

Route::post('/fcm-tokens', [FcmController::class, 'store']);
Route::delete('/fcm-tokens', [FcmController::class, 'destroy']);

Route::post('/fcm/update', [FcmController::class, 'update']);
Route::post('/fcm/destroy', [FcmController::class, 'destroy']);
Route::post('/fcm/test', [FcmController::class, 'testToken']);
