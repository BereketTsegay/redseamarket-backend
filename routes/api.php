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
Route::post('/user/forgot/send/password/toMail', [\App\Http\Controllers\Api\LoginController::class, 'sendPasswordToMail']);
Route::post('/customer/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'dashboard']);

    // Ads Section
    
Route::post('/customer/ads/custom_field_and_dependency', [\App\Http\Controllers\Api\AdsController::class, 'customFieldsAndDependency']);
Route::post('/customer/get/master/dependency', [\App\Http\Controllers\Api\AdsController::class, 'getMasterDependency']);
Route::post('/customer/ads/store', [\App\Http\Controllers\Api\AdsController::class, 'adStore']);
Route::post('/customer/get/category', [\App\Http\Controllers\Api\DashboardController::class, 'getCategory']);
Route::post('/customer/get/subcategory', [\App\Http\Controllers\Api\DashboardController::class, 'getSubcategory']);
Route::post('/customer/view/favourite', [\App\Http\Controllers\Api\OtherController::class, 'favouriteView']);
Route::post('/customer/view/myAds', [\App\Http\Controllers\Api\OtherController::class, 'myAds']);
Route::post('/customer/favourite/adOrRemove', [\App\Http\Controllers\Api\OtherController::class, 'favouriteStoreOrRemove']);
Route::post('/customer/ad/view', [\App\Http\Controllers\Api\AdsController::class, 'adView']);
Route::post('/customer/search/ads', [\App\Http\Controllers\Api\OtherController::class, 'searchAds']);

Route::middleware('auth:api')->group( function () {
    
});

