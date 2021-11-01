<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('test', [\App\Http\Controllers\Api\LoginController::class, 'newTest']);

Route::get('/', function(){
    return redirect()->route('login.index');
});
Route::get('/forgotpassword/index', [App\Http\Controllers\LoginController::class, 'forgotPasswordIndex'])->name('forgotpassword.index');
Route::post('/forgotpassword/store', [App\Http\Controllers\LoginController::class, 'forgotPasswordStore'])->name('forgotpassword.store');

// Logout user Back button Cache clearing
Route::group(['middleware' => ['revalidate']], function(){

    Route::get('/login', [App\Http\Controllers\LoginController::class, 'index'])->name('login.index');
    Route::post('/login/store', [App\Http\Controllers\LoginController::class, 'store'])->name('login.store');
    
    Route::post('/fcm/token/store', [App\Http\Controllers\LoginController::class, 'tokenStore'])->name('save.token');
    Route::get('/fcm/notification/send', [App\Http\Controllers\LoginController::class, 'sendNotification'])->name('send.token');
    
    // Middleware Prevent Unautherized access
    Route::group(['middleware' => ['adminAuth']], function(){

        // Route::group(['middleware' => ['superAdmin']], function(){
            
            // Admin Users

            Route::get('/role', [App\Http\Controllers\UserRoleController::class, 'index'])->name('role.index');
            Route::post('/role/store', [App\Http\Controllers\UserRoleController::class, 'store'])->name('role.store');
            Route::get('/role/edit/{id}', [App\Http\Controllers\UserRoleController::class, 'edit'])->name('role.edit');
            Route::post('/role/update', [App\Http\Controllers\UserRoleController::class, 'update'])->name('role.update');
            Route::post('/role/delete/{id}', [App\Http\Controllers\UserRoleController::class, 'delete'])->name('role.delete');

            Route::post('/task/role/store', [App\Http\Controllers\UserRoleController::class, 'taskRoleStore'])->name('task_role.store');
            Route::post('/task/role/update/{id}', [App\Http\Controllers\UserRoleController::class, 'taskRoleUpdate'])->name('task_role.update');

            Route::get('/admin/user/index', [App\Http\Controllers\UserRoleController::class, 'adminUserIndex'])->name('admin_user.index');
            Route::get('/admin/user/create', [App\Http\Controllers\UserRoleController::class, 'adminUserCreate'])->name('admin_user.create');
            Route::post('/admin/user/store', [App\Http\Controllers\UserRoleController::class, 'adminUserStore'])->name('admin_user.store');
            Route::get('/admin/user/view/{id}', [App\Http\Controllers\UserRoleController::class, 'adminUserView'])->name('admin_user.view');
            Route::get('/admin/user/edit/{id}', [App\Http\Controllers\UserRoleController::class, 'adminUserEdit'])->name('admin_user.edit');
            Route::post('/admin/user/update/{id}', [App\Http\Controllers\UserRoleController::class, 'adminUserUpdate'])->name('admin_user.update');
            
        // });

        // Global

        Route::get('/global/state/get', [App\Http\Controllers\CategoryController::class, 'getState']);
        Route::get('/global/city/get', [App\Http\Controllers\CategoryController::class, 'getCity']);
        Route::get('/global/vehicle/model/get', [App\Http\Controllers\CategoryController::class, 'getVehicleModel']);
        Route::get('/global/vehicle/varient/get', [App\Http\Controllers\CategoryController::class, 'getVehicleVarient']);

        Route::post('/admin/change/password', [App\Http\Controllers\LoginController::class, 'changePassword'])->name('admin.change.password');
        Route::get('/admin/profile', [App\Http\Controllers\LoginController::class, 'profile'])->name('admin.profile');
        Route::get('/admin/profile/edit/{id}', [App\Http\Controllers\LoginController::class, 'profileEdit'])->name('admin.profile.edit');
        Route::post('/admin/profile/update/{id}', [App\Http\Controllers\LoginController::class, 'profileUpdate'])->name('admin.profile.update');

        Route::get('dashboard', [App\Http\Controllers\LoginController::class, 'dashboard'])->name('dashboard');

        // Users

        Route::get('/users', [App\Http\Controllers\LoginController::class, 'userIndex'])->name('user.index');
        Route::get('/users/view/{id}', [App\Http\Controllers\LoginController::class, 'userView'])->name('user.view');
        Route::get('/users/edit/{id}', [App\Http\Controllers\LoginController::class, 'userEdit'])->name('user.edit');
        Route::post('/users/update/{id}', [App\Http\Controllers\LoginController::class, 'userUpdate'])->name('user.update');
        Route::get('/users/ads/{type}/{id}', [App\Http\Controllers\LoginController::class, 'userAds'])->name('user.ads');
        Route::post('/users/change/password/{id}', [App\Http\Controllers\LoginController::class, 'userChangePassword'])->name('user.change.password');
        Route::post('/users/delete/{id}', [App\Http\Controllers\UserRoleController::class, 'userDelete'])->name('user.delete');

        /* ========== Ads ========== */

            // Category

        Route::get('/category', [App\Http\Controllers\CategoryController::class, 'index'])->name('category.index');
        Route::get('/category/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('category.create');
        Route::post('/category/store', [App\Http\Controllers\CategoryController::class, 'store'])->name('category.store');
        Route::get('/category/view/{id}', [App\Http\Controllers\CategoryController::class, 'view'])->name('category.view');
        Route::get('/category/edit/{id}', [App\Http\Controllers\CategoryController::class, 'edit'])->name('category.edit');
        Route::post('/category/update/{id}', [App\Http\Controllers\CategoryController::class, 'update'])->name('category.update');
        Route::post('/category/delete/{id}', [App\Http\Controllers\CategoryController::class, 'delete'])->name('category.delete');

            // Subcategory

        Route::get('/subcategory', [App\Http\Controllers\SubcategoryController::class, 'index'])->name('subcategory.index');
        Route::get('/subcategory/create', [App\Http\Controllers\SubcategoryController::class, 'create'])->name('subcategory.create');
        Route::post('/subcategory/store', [App\Http\Controllers\SubcategoryController::class, 'store'])->name('subcategory.store');
        Route::get('/subcategory/edit/{id}', [App\Http\Controllers\SubcategoryController::class, 'edit'])->name('subcategory.edit');
        Route::post('/subcategory/update/{id}', [App\Http\Controllers\SubcategoryController::class, 'update'])->name('subcategory.update');
        Route::get('/subcategory/view/{id}', [App\Http\Controllers\SubcategoryController::class, 'view'])->name('subcategory.view');
        Route::post('/subcategory/delete/{id}', [App\Http\Controllers\SubcategoryController::class, 'delete'])->name('subcategory.delete');

        Route::get('/change/subcategory', [App\Http\Controllers\SubcategoryController::class, 'subcategoryAjaxfetch']);
        Route::get('/change/subcategory/category', [App\Http\Controllers\SubcategoryController::class, 'subcategoryChange']);

            // Icons
        
        Route::get('/icons', [App\Http\Controllers\IconController::class, 'index'])->name('icon.index');
        Route::post('/icon/store', [App\Http\Controllers\IconController::class, 'store'])->name('icon.store');
        Route::post('/icon/update', [App\Http\Controllers\IconController::class, 'update'])->name('icon.update');
        Route::post('/icon/delete/{id}', [App\Http\Controllers\IconController::class, 'delete'])->name('icon.delete');


            // Custom Field

        Route::get('/custom_field', [App\Http\Controllers\CustomFieldController::class, 'index'])->name('custom_field.index');
        Route::get('/custom_field/create', [App\Http\Controllers\CustomFieldController::class, 'create'])->name('custom_field.create');
        Route::post('/custom_field/store', [App\Http\Controllers\CustomFieldController::class, 'store'])->name('custom_field.store');
        Route::get('/custom_field/view/{id}', [App\Http\Controllers\CustomFieldController::class, 'view'])->name('custom_field.view');
        Route::get('/custom_field/edit/{id}', [App\Http\Controllers\CustomFieldController::class, 'edit'])->name('custom_field.edit');
        Route::post('/custom_field/update/{id}', [App\Http\Controllers\CustomFieldController::class, 'update'])->name('custom_field.update');
        Route::post('/custom_field/delete/{id}', [App\Http\Controllers\CustomFieldController::class, 'delete'])->name('custom_field.delete');

                // Dependency
        
        Route::get('/dependency/get', [App\Http\Controllers\CustomFieldController::class, 'dependencyGet'])->name('dependency.get.ajax');
        Route::get('/dependency/get/dependent', [App\Http\Controllers\CustomFieldController::class, 'dependencyGetDependent'])->name('dependency.get.dependent.ajax');
        Route::post('/dependency/delete/dependent/{id}', [App\Http\Controllers\CustomFieldController::class, 'customDependencyDelete'])->name('custom.dependency.delete');

                // Option

        Route::get('/custom_field/option/index/{id}', [App\Http\Controllers\CustomFieldController::class, 'optionIndex'])->name('custom_field.option.index');
        Route::post('/custom_field/option/create/{id}', [App\Http\Controllers\CustomFieldController::class, 'optionCreate'])->name('custom_field.option.create');
        Route::post('/custom_field/option/delete/{id}', [App\Http\Controllers\CustomFieldController::class, 'optionDelete'])->name('custom_field.option.delete');

                // Add to Category

        Route::post('/custom_field/addtocategory', [App\Http\Controllers\CustomFieldController::class, 'addtoCategory'])->name('custom_field.addtocategory');
        Route::post('/custom_field/deletefromcategory/{id}', [App\Http\Controllers\CustomFieldController::class, 'deleteFromCategory'])->name('custom_field.deletefromcategory');
        

            // Ads

        Route::get('/ad_list', [App\Http\Controllers\AdsController::class, 'index'])->name('ads.index');
        Route::get('/ad/create', [App\Http\Controllers\AdsController::class, 'create'])->name('ads.create');
        Route::post('/ad/store', [App\Http\Controllers\AdsController::class, 'store'])->name('ads.store');
        Route::get('/ad/view/{id}', [App\Http\Controllers\AdsController::class, 'view'])->name('ads.view');
        Route::get('/ad/edit/{id}', [App\Http\Controllers\AdsController::class, 'edit'])->name('ads.edit');
        Route::post('/ad/update/{id}', [App\Http\Controllers\AdsController::class, 'update'])->name('ads.update');
        Route::post('/ad/delete/{id}', [App\Http\Controllers\AdsController::class, 'delete'])->name('ads.delete');

        Route::get('/get/custom/field', [App\Http\Controllers\AdsController::class, 'getCustomField'])->name('ad.get.custom_field');
        Route::get('/get/master/dependency', [App\Http\Controllers\AdsController::class, 'getMasterDependency'])->name('ad.get.master.dependency');
        Route::get('/ads/related/field', [App\Http\Controllers\AdsController::class, 'getAdsRelated']);
        Route::get('/get/motor/feature', [App\Http\Controllers\AdsController::class, 'getMotorFeature']);

                // Ads Request

        Route::get('/ad_request', [App\Http\Controllers\AdsController::class, 'adRequestIndex'])->name('ad_request.index');
        Route::get('/ad_request/details/{id}', [App\Http\Controllers\AdsController::class, 'adRequestDetails'])->name('ad_request.details');
        Route::post('/ad/accept/{id}', [App\Http\Controllers\AdsController::class, 'adAccept'])->name('ad.accept');

        Route::get('/get/reject/reson', [App\Http\Controllers\AdsController::class, 'getRejectReson']);
        Route::post('/ad/reject', [App\Http\Controllers\AdsController::class, 'adReject'])->name('reject.ads');
        Route::post('/ad/refund', [App\Http\Controllers\AdsController::class, 'adRefund'])->name('refund.ads');

            // Banner

        Route::get('/banner', [App\Http\Controllers\BannerController::class, 'index'])->name('banner.index');
        Route::post('/banner/store', [App\Http\Controllers\BannerController::class, 'store'])->name('banner.store');
        Route::get('/banner/view/{id}', [App\Http\Controllers\BannerController::class, 'view'])->name('banner.view');
        Route::post('/banner/update', [App\Http\Controllers\BannerController::class, 'update'])->name('banner.update');
        Route::post('/banner/delete/{id}', [App\Http\Controllers\BannerController::class, 'delete'])->name('banner.delete');

            // Social
        
        Route::get('/social', [App\Http\Controllers\SocialLinkController::class, 'index'])->name('social.index');
        Route::post('/social/store', [App\Http\Controllers\SocialLinkController::class, 'store'])->name('social.store');
        Route::get('/social/edit/{id}', [App\Http\Controllers\SocialLinkController::class, 'edit'])->name('social.edit');
        Route::post('/social/update/{id}', [App\Http\Controllers\SocialLinkController::class, 'update'])->name('social.update');
        Route::post('/social/delete/{id}', [App\Http\Controllers\SocialLinkController::class, 'delete'])->name('social.delete');

            // Testimonial
        
        Route::get('/testimonial', [App\Http\Controllers\TestimonialController::class, 'index'])->name('testimonial.index');
        Route::post('/testimonial/store', [App\Http\Controllers\TestimonialController::class, 'store'])->name('testimonial.store');
        Route::get('/testimonial/view/{id}', [App\Http\Controllers\TestimonialController::class, 'view'])->name('testimonial.view');
        Route::get('/testimonial/edit/{id}', [App\Http\Controllers\TestimonialController::class, 'edit'])->name('testimonial.edit');
        Route::post('/testimonial/update/{id}', [App\Http\Controllers\TestimonialController::class, 'update'])->name('testimonial.update');

            // Reson
        
        Route::get('/reject/reson', [App\Http\Controllers\RejectResonController::class, 'index'])->name('reject.index');
        Route::post('/reject/reson/store', [App\Http\Controllers\RejectResonController::class, 'store'])->name('reject.store');
        Route::post('/reject/reson/update', [App\Http\Controllers\RejectResonController::class, 'update'])->name('reject.update');

            // Payment

        Route::get('/payment', [App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
        Route::get('/payment/view/{id}', [App\Http\Controllers\PaymentController::class, 'view'])->name('payment.view');
        Route::post('/payment/update/{id}', [App\Http\Controllers\PaymentController::class, 'update'])->name('payment.update');

            // Featured Dealer

        Route::get('/featured/dealer', [App\Http\Controllers\IconController::class, 'featuredIndex'])->name('dealer.index');
        Route::post('/featured/dealer/store', [App\Http\Controllers\IconController::class, 'featuredStore'])->name('dealer.store');
        Route::post('/featured/dealer/update', [App\Http\Controllers\IconController::class, 'featuredUpdate'])->name('dealer.update');
        Route::post('/featured/dealer/delete/{id}', [App\Http\Controllers\IconController::class, 'featuredDelete'])->name('dealer.delete');

            // Privacy Policy

        Route::get('/privacy', [App\Http\Controllers\PrivacyPolicyController::class, 'index'])->name('privacy.index');
        Route::post('/privacy/store', [App\Http\Controllers\PrivacyPolicyController::class, 'store'])->name('privacy.store');
        Route::post('/privacy/update', [App\Http\Controllers\PrivacyPolicyController::class, 'update'])->name('privacy.update');
        Route::post('/privacy/delete/{id}', [App\Http\Controllers\PrivacyPolicyController::class, 'delete'])->name('privacy.delete');

            // Terms Conditions

        Route::get('/terms', [App\Http\Controllers\TermsConditionsController::class, 'index'])->name('terms.index');
        Route::post('/terms/store', [App\Http\Controllers\TermsConditionsController::class, 'store'])->name('terms.store');
        Route::post('/terms/update', [App\Http\Controllers\TermsConditionsController::class, 'update'])->name('terms.update');
        Route::post('/terms/delete/{id}', [App\Http\Controllers\TermsConditionsController::class, 'delete'])->name('terms.delete');

        // Contact us enquiry

        Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
        Route::get('/contact/view/{id}', [App\Http\Controllers\ContactController::class, 'view'])->name('contact.view');
        Route::post('/contact/replay/mail/{id}', [App\Http\Controllers\ContactController::class, 'replay'])->name('send.mail.replay');

        Route::get('/get/notification', [App\Http\Controllers\AdsController::class,'adNotification']);
        Route::post('/read/notification', [App\Http\Controllers\AdsController::class,'readNotification']);

        Route::post('/upload/document/{id}', [App\Http\Controllers\PaymentController::class, 'documentUpload'])->name('payment.document.upload');

        Route::get('/admin/logout', function(){
            Auth::logout();
    
            return redirect()->route('login.index');
        })->name('logout');
    });
    
});

// Route::view('/test', 'enquiry_replay');