<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user/login', [\App\Http\Controllers\Api\LoginController::class, 'login']);
Route::post('/user/register', [\App\Http\Controllers\Api\LoginController::class, 'register']);
Route::post('/customer/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'dashboard']);

    // Ads Section
    
Route::post('/customer/ads/custom_field_and_dependency', [\App\Http\Controllers\Api\AdsController::class, 'customFieldsAndDependency']);
Route::post('/customer/get/master/dependency', [\App\Http\Controllers\Api\AdsController::class, 'getMasterDependency']);
Route::post('/customer/ads/store', [\App\Http\Controllers\Api\AdsController::class, 'adStore']);
Route::post('/customer/get/category', [\App\Http\Controllers\Api\DashboardController::class, 'getCategory']);
Route::post('/customer/get/subcategory', [\App\Http\Controllers\Api\DashboardController::class, 'getSubcategory']);

Route::middleware('auth:api')->group( function () {
    
});

