<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['namespace'=>'App','prefix' => 'app'],function (){

    Route::post('/user/login', [\App\Http\Controllers\App\LoginController::class, 'login']);
    Route::post('/user/register', [\App\Http\Controllers\App\LoginController::class, 'register']);
    Route::post('/forgot/password/send/toMail', [\App\Http\Controllers\App\LoginController::class, 'sendPasswordToMail']);
    Route::post('/customer/city/list', [\App\Http\Controllers\App\OtherController::class, 'cityList']);
  //  Route::post('/verify/email', [\App\Http\Controllers\App\LoginController::class, 'vefifyEmail']);
    Route::post('/verify/otp', [\App\Http\Controllers\App\LoginController::class, 'verifyOtp']);
    Route::post('/forgotpassword/password/reset', [\App\Http\Controllers\App\LoginController::class, 'passwordReset']);
    Route::post('/verify/resent/otp', [\App\Http\Controllers\App\LoginController::class, 'newOtp']);
    
    // Ads Section
    // Route::post('/customer/ads/get_details', [\App\Http\Controllers\App\AdsController::class, 'getDetails']);  
    Route::post('/customer/ads/custom_field_and_dependency', [\App\Http\Controllers\App\AdsController::class, 'customFieldsAndDependency']);
    Route::post('/customer/get/master/dependency', [\App\Http\Controllers\App\AdsController::class, 'getMasterDependency']);
    Route::post('/customer/get/category', [\App\Http\Controllers\App\DashboardController::class, 'getCategory']);
    Route::post('/customer/get/subcategory', [\App\Http\Controllers\App\DashboardController::class, 'getSubcategory']);
    Route::post('/customer/get/subsubcategory', [\App\Http\Controllers\App\DashboardController::class, 'getSubSubcategory']);
    Route::post('/customer/get/job/subsubcategory', [\App\Http\Controllers\App\DashboardController::class, 'getjobSubSubcategory']);
    
    
    Route::post('/customer/search/ads', [\App\Http\Controllers\App\OtherController::class, 'searchAds']);
    Route::post('/customer/get/subcategory/ads', [\App\Http\Controllers\App\OtherController::class, 'getSubcategoryAds']);
    Route::post('/customer/get/property/filter', [\App\Http\Controllers\App\AdsController::class, 'getPropertyFilter']);
    Route::post('/customer/get/motor/list', [\App\Http\Controllers\App\OtherController::class, 'getMototList']);
    Route::post('/customer/get/job/list', [\App\Http\Controllers\App\OtherController::class, 'getJobList']);
    
    Route::post('/customer/get/country', [\App\Http\Controllers\App\OtherController::class,'getCountry']);
    Route::post('/customer/get/state', [\App\Http\Controllers\App\OtherController::class, 'getState']);
    Route::post('/customer/get/city', [\App\Http\Controllers\App\OtherController::class, 'getCity']);
    Route::post('/customer/get/motors', [\App\Http\Controllers\App\AdsController::class, 'getCategoryMotors']);
    Route::post('/customer/get/property', [\App\Http\Controllers\App\AdsController::class, 'getProperty']);
    Route::post('/customer/search/motors', [\App\Http\Controllers\App\AdsController::class, 'motorSearch']);
    Route::post('/customer/social/link', [\App\Http\Controllers\App\OtherController::class,'socialLink']);
    Route::post('/customer/ads/view/countupdate', [\App\Http\Controllers\App\AdsController::class, 'adsViewEntry']);
    
    Route::post('/customer/get/featured/dealer', [\App\Http\Controllers\App\OtherController::class,'featuredDealer']);
    
    Route::post('/stripe/payment', [\App\Http\Controllers\App\OtherController::class,'recivePayment']);
    
    Route::post('/get/currency', [\App\Http\Controllers\App\OtherController::class,'getCurrency']);
    Route::post('/payment/status/update', [\App\Http\Controllers\App\OtherController::class,'paymentStatusUpdate']);
    Route::post('/subcategory/featured/amount', [\App\Http\Controllers\App\OtherController::class,'getFeaturedAmount']);
    
    Route::post('/customer/get/make', [\App\Http\Controllers\App\AdsController::class, 'getMake']);
    Route::post('/customer/get/model', [\App\Http\Controllers\App\AdsController::class, 'getModel']);
    Route::post('/customer/get/variant', [\App\Http\Controllers\App\AdsController::class, 'getVarieant']);
    
    Route::post('/customer/get/home/banner', [\App\Http\Controllers\App\OtherController::class, 'getHomeBanner']);
    
    // ad enquiry
    Route::post('/customer/ads/enquiry', [\App\Http\Controllers\App\OtherController::class, 'adEnquiry']);
    Route::post('/customer/get/ad/enquiry', [\App\Http\Controllers\App\OtherController::class, 'adEnquirylist']);
    
    
    Route::post('/privacy/policy', [\App\Http\Controllers\App\OtherController::class, 'privacyPolicy']);
    Route::post('/terms/conditions', [\App\Http\Controllers\App\OtherController::class, 'termsConditions']);
    
    Route::post('/search/autocomplete', [\App\Http\Controllers\App\AdsController::class, 'searchAutoComplete']);
    
    Route::post('/contactus/enquiry', [\App\Http\Controllers\App\OtherController::class, 'contactEnquiry']);
    
    Route::post('/menu/list', [\App\Http\Controllers\App\DashboardController::class, 'MenuList']);
    Route::get('/category/list', [\App\Http\Controllers\App\OtherController::class, 'allCategories']);
    Route::get('/featured', [\App\Http\Controllers\App\AdsController::class, 'featured']);


});


    Route::group(['namespace' => 'App','middleware' => 'auth:api','prefix' => 'app'], function () {

    // Route::post('/customer/loged/dashboard', [\App\Http\Controllers\App\DashboardController::class, 'LogedDashboard']);
    Route::post('/customer/dashboard', [\App\Http\Controllers\App\DashboardController::class, 'dashboard']);
    Route::post('/customer/ads/store', [\App\Http\Controllers\App\AdsController::class, 'adStore']);
    Route::post('/customer/ads/update', [\App\Http\Controllers\App\AdsController::class, 'updateData']);
    Route::post('/customer/view/profile', [\App\Http\Controllers\App\LoginController::class, 'myProfile']);
    Route::post('/customer/view/transactions', [\App\Http\Controllers\App\OtherController::class, 'Transactions']);
    Route::post('/customer/update/profile', [\App\Http\Controllers\App\LoginController::class, 'updateProfile']);
    Route::post('/customer/ad/favourite', [\App\Http\Controllers\App\AdsController::class, 'favouriteGet']);
    Route::post('/customer/view/favourite', [\App\Http\Controllers\App\OtherController::class, 'favouriteView']);
    Route::post('/customer/view/myAds', [\App\Http\Controllers\App\OtherController::class, 'myAds']);
    Route::post('/customer/favourite/adOrRemove', [\App\Http\Controllers\App\OtherController::class, 'favouriteStoreOrRemove']);
    Route::post('/customer/change/password', [\App\Http\Controllers\App\LoginController::class, 'changePassword']);
    Route::post('/customer/uploade/payment_slip', [\App\Http\Controllers\App\OtherController::class, 'paymentDocument']);
    Route::post('/customer/get/ad-selCountry', [\App\Http\Controllers\App\AdsController::class, 'adsCountries']);
    Route::post('/customer/get/ad-cvdocuments', [\App\Http\Controllers\App\AdsController::class, 'jobRequestDocs']);

    Route::post('/customer/get/category/ads', [\App\Http\Controllers\App\OtherController::class, 'getCategoryAds']);
    Route::post('/customer/ad/view', [\App\Http\Controllers\App\AdsController::class, 'adView']);

    Route::post('/category/image/update', [\App\Http\Controllers\App\AdsController::class, 'categoryImage']);

    Route::post('/customer/logout', [\App\Http\Controllers\App\LoginController::class, 'logout']);
    Route::post('/customer/ads/remove_image', [\App\Http\Controllers\App\AdsController::class, 'removeImage']);
    Route::post('/customer/ad/delete', [\App\Http\Controllers\App\AdsController::class, 'removeAd']);
    Route::post('/apply/job', [\App\Http\Controllers\App\OtherController::class, 'saveJobrequest']);
    Route::post('/check/user/apply_document', [\App\Http\Controllers\App\OtherController::class, 'checkDocument']); 
    
    Route::post('/get/jobprofile', [\App\Http\Controllers\App\OtherController::class, 'jobProfile']);
    Route::post('/save/jobprofile', [\App\Http\Controllers\App\OtherController::class, 'jobProfileSave']);
    Route::post('/update/jobprofile', [\App\Http\Controllers\App\OtherController::class, 'jobProfileUpdate']);
    Route::post('/get/jobprofile/list', [\App\Http\Controllers\App\OtherController::class, 'jobProfileList']);
    Route::post('/get/jobprofile/detail', [\App\Http\Controllers\App\OtherController::class, 'jobProfileDetails']);

});