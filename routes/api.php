<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\SchoolClassController;

Route::get('/home/products' , HomePageController::class);
Route::get('/products/{product:slug}' , ProductController::class);
Route::get('/search/filters' , [SearchController::class , 'getFilters']);
Route::get('/search' , [SearchController::class , 'search']);
Route::get('/ads' , AdController::class);
Route::get('/school/classes/{schoolClass}' , SchoolClassController::class);
Route::get('/contacts' , ContactController::class);