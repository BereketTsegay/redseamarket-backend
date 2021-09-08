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
Route::post('/customer/city/list', [\App\Http\Controllers\Api\OtherController::class, 'cityList']);
Route::post('/verify/email', [\App\Http\Controllers\Api\LoginController::class, 'vefifyEmail']);
    // Ads Section
    
Route::post('/customer/ads/custom_field_and_dependency', [\App\Http\Controllers\Api\AdsController::class, 'customFieldsAndDependency']);
Route::post('/customer/get/master/dependency', [\App\Http\Controllers\Api\AdsController::class, 'getMasterDependency']);

Route::post('/customer/get/category', [\App\Http\Controllers\Api\DashboardController::class, 'getCategory']);
Route::post('/customer/get/subcategory', [\App\Http\Controllers\Api\DashboardController::class, 'getSubcategory']);

Route::post('/customer/ad/view', [\App\Http\Controllers\Api\AdsController::class, 'adView']);
Route::post('/customer/search/ads', [\App\Http\Controllers\Api\OtherController::class, 'searchAds']);
Route::post('/customer/get/category/ads', [\App\Http\Controllers\Api\OtherController::class, 'getCategoryAds']);
Route::post('/customer/get/subcategory/ads', [\App\Http\Controllers\Api\OtherController::class, 'getSubcategoryAds']);
Route::post('/customer/get/property/filter', [\App\Http\Controllers\Api\AdsController::class, 'getPropertyFilter']);
Route::post('/customer/get/motor/list', [\App\Http\Controllers\Api\OtherController::class, 'getMototList']);

Route::post('/customer/get/country', [\App\Http\Controllers\Api\OtherController::class,'getCountry']);
Route::post('/customer/get/state', [\App\Http\Controllers\Api\OtherController::class, 'getState']);
Route::post('/customer/get/city', [\App\Http\Controllers\Api\OtherController::class, 'getCity']);
Route::post('/customer/get/motors', [\App\Http\Controllers\Api\AdsController::class, 'getCategoryMotors']);
Route::post('/customer/get/property', [\App\Http\Controllers\Api\AdsController::class, 'getProperty']);
Route::post('/customer/search/motors', [\App\Http\Controllers\Api\AdsController::class, 'motorSearch']);
Route::post('/customer/social/link', [\App\Http\Controllers\Api\OtherController::class,'socialLink']);
Route::post('/customer/ads/view/countupdate', [\App\Http\Controllers\Api\AdsController::class, 'adsViewEntry']);

Route::post('/customer/get/featured/dealer', [\App\Http\Controllers\Api\OtherController::class,'featuredDealer']);

Route::post('/stripe/payment', [\App\Http\Controllers\Api\OtherController::class,'recivePayment']);

Route::post('/get/currency', [\App\Http\Controllers\Api\OtherController::class,'getCurrency']);
Route::post('/payment/status/update', [\App\Http\Controllers\Api\OtherController::class,'paymentStatusUpdate']);
Route::post('/subcategory/featured/amount', [\App\Http\Controllers\Api\OtherController::class,'getFeaturedAmount']);

Route::post('/customer/get/make', [\App\Http\Controllers\Api\AdsController::class, 'getMake']);
Route::post('/customer/get/model', [\App\Http\Controllers\Api\AdsController::class, 'getModel']);
Route::post('/customer/get/variant', [\App\Http\Controllers\Api\AdsController::class, 'getVarieant']);

// ad enquiry
Route::post('/customer/ads/enquiry', [\App\Http\Controllers\Api\OtherController::class, 'adEnquiry']);

Route::middleware('auth:api')->group( function () {

    Route::post('/customer/loged/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'LogedDashboard']);

    Route::post('/customer/ads/store', [\App\Http\Controllers\Api\AdsController::class, 'adStore']);
    Route::post('/customer/view/profile', [\App\Http\Controllers\Api\LoginController::class, 'myProfile']);
    Route::post('/customer/update/profile', [\App\Http\Controllers\Api\LoginController::class, 'updateProfile']);
    Route::post('/customer/ad/favourite', [\App\Http\Controllers\Api\AdsController::class, 'favouriteGet']);
    Route::post('/customer/view/favourite', [\App\Http\Controllers\Api\OtherController::class, 'favouriteView']);
    Route::post('/customer/view/myAds', [\App\Http\Controllers\Api\OtherController::class, 'myAds']);
    Route::post('/customer/favourite/adOrRemove', [\App\Http\Controllers\Api\OtherController::class, 'favouriteStoreOrRemove']);
    Route::post('/customer/change/password', [\App\Http\Controllers\Api\LoginController::class, 'changePassword']);
    Route::post('/customer/uploade/payment_slip', [\App\Http\Controllers\Api\OtherController::class, 'paymentDocument']);

    Route::post('/customer/logout', [\App\Http\Controllers\Api\LoginController::class, 'logout']);
});

