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

Route::get('/', function () {
    return redirect()->route('login.index');
});
Route::get('ad/expire/cron',[App\Http\Controllers\AdsController::class,'expireCheck']);
Route::get('/forgotpassword/index', [App\Http\Controllers\LoginController::class, 'forgotPasswordIndex'])->name('forgotpassword.index');
Route::post('/forgotpassword/store', [App\Http\Controllers\LoginController::class, 'forgotPasswordStore'])->name('forgotpassword.store');

// Logout user Back button Cache clearing
Route::group(['middleware' => ['revalidate']], function () {

    Route::get('/login', [App\Http\Controllers\LoginController::class, 'index'])->name('login.index');
    Route::post('/login/store', [App\Http\Controllers\LoginController::class, 'store'])->name('login.store');

    Route::post('/fcm/token/store', [App\Http\Controllers\LoginController::class, 'tokenStore'])->name('save.token');
    Route::get('/fcm/notification/send', [App\Http\Controllers\LoginController::class, 'sendNotification'])->name('send.token');

    // Middleware Prevent Unautherized access
    Route::group(['middleware' => ['adminAuth']], function () {

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

        // featured

        Route::get('/featured', [App\Http\Controllers\FeaturedController::class, 'index'])->name('admin.featured');
        Route::post('/featured/update', [App\Http\Controllers\FeaturedController::class, 'update'])->name('admin.featured.update');


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
        Route::post('/users/delete/{id}', [App\Http\Controllers\LoginController::class, 'userDelete'])->name('user.delete');

        /* ========== Ads ========== */
        // MakeMst

        Route::get('/make_mst', [App\Http\Controllers\MakeMstController::class, 'index'])->name('make_mst.index');
        Route::get('/make_mst/create', [App\Http\Controllers\MakeMstController::class, 'create'])->name('make_mst.create');
        Route::post('/make_mst/store', [App\Http\Controllers\MakeMstController::class, 'store'])->name('make_mst.store');
        Route::get('/make_mst/view/{id}', [App\Http\Controllers\MakeMstController::class, 'view'])->name('make_mst.view');
        Route::get('/make_mst/edit/{id}', [App\Http\Controllers\MakeMstController::class, 'edit'])->name('make_mst.edit');
        Route::post('/make_mst/update/{id}', [App\Http\Controllers\MakeMstController::class, 'update'])->name('make_mst.update');
        Route::post('/make_mst/delete/{id}', [App\Http\Controllers\MakeMstController::class, 'delete'])->name('make_mst.delete');

        // ModelMst

        Route::get('/model_mst', [App\Http\Controllers\ModelMstController::class, 'index'])->name('model_mst.index');
        Route::get('/model_mst/create', [App\Http\Controllers\ModelMstController::class, 'create'])->name('model_mst.create');
        Route::post('/model_mst/store', [App\Http\Controllers\ModelMstController::class, 'store'])->name('model_mst.store');
        Route::get('/model_mst/edit/{id}', [App\Http\Controllers\ModelMstController::class, 'edit'])->name('model_mst.edit');
        Route::post('/model_mst/update/{id}', [App\Http\Controllers\ModelMstController::class, 'update'])->name('model_mst.update');
        Route::post('/model_mst/delete/{id}', [App\Http\Controllers\ModelMstController::class, 'delete'])->name('model_mst.delete');

         // VarientMst

         Route::get('/variant_mst', [App\Http\Controllers\VarientMstController::class, 'index'])->name('varient_mst.index');
         Route::get('/variant_mst/create', [App\Http\Controllers\VarientMstController::class, 'create'])->name('varient_mst.create');
         Route::post('/variant_mst/store', [App\Http\Controllers\VarientMstController::class, 'store'])->name('varient_mst.store');
         Route::get('/variant_mst/edit/{id}', [App\Http\Controllers\VarientMstController::class, 'edit'])->name('varient_mst.edit');
         Route::post('/variant_mst/update/{id}', [App\Http\Controllers\VarientMstController::class, 'update'])->name('varient_mst.update');
         Route::post('/variant_mst/delete/{id}', [App\Http\Controllers\VarientMstController::class, 'delete'])->name('varient_mst.delete');
       
         //country
         Route::get('/countries', [App\Http\Controllers\ConntryController::class, 'CountryIndex'])->name('countries.index');
         Route::get('/countries/create', [App\Http\Controllers\ConntryController::class, 'CountryCreate'])->name('countries.create');
         Route::post('/countries/store', [App\Http\Controllers\ConntryController::class, 'CountryStore'])->name('countries.store');
         Route::get('/countries/edit/{id}', [App\Http\Controllers\ConntryController::class, 'CountryEdit'])->name('countries.edit');
         Route::post('/countries/update/{id}', [App\Http\Controllers\ConntryController::class, 'CountryUpdate'])->name('countries.update');
         Route::post('/countries/delete/{id}', [App\Http\Controllers\ConntryController::class, 'CountryDelete'])->name('countries.delete');


        //state
        Route::get('/states', [App\Http\Controllers\CityStateController::class, 'stateIndex'])->name('states.index');
        Route::get('/states/create', [App\Http\Controllers\CityStateController::class, 'stateCreate'])->name('states.create');
        Route::post('/states/store', [App\Http\Controllers\CityStateController::class, 'stateStore'])->name('states.store');
        Route::get('/states/edit/{id}', [App\Http\Controllers\CityStateController::class, 'stateEdit'])->name('states.edit');
        Route::post('/states/update/{id}', [App\Http\Controllers\CityStateController::class, 'stateUpdate'])->name('states.update');
        Route::post('/states/delete/{id}', [App\Http\Controllers\CityStateController::class, 'stateDelete'])->name('states.delete');

        //city
        Route::get('/cities', [App\Http\Controllers\CityStateController::class, 'cityIndex'])->name('cities.index');
        Route::get('/cities/create', [App\Http\Controllers\CityStateController::class, 'cityCreate'])->name('cities.create');
        Route::post('/cities/store', [App\Http\Controllers\CityStateController::class, 'cityStore'])->name('cities.store');
        Route::get('/cities/edit/{id}', [App\Http\Controllers\CityStateController::class, 'cityEdit'])->name('cities.edit');
        Route::post('/cities/update/{id}', [App\Http\Controllers\CityStateController::class, 'cityUpdate'])->name('cities.update');
        Route::post('/cities/delete/{id}', [App\Http\Controllers\CityStateController::class, 'cityDelete'])->name('cities.delete');

         // Conunty Currency
         Route::get('/country_currency', [App\Http\Controllers\ConntryController::class, 'CurrencyIndex'])->name('country_currency.index');
         Route::get('/country_currency/create', [App\Http\Controllers\ConntryController::class, 'CurrencyCreate'])->name('country_currency.create');
         Route::post('/country_currency/store', [App\Http\Controllers\ConntryController::class, 'CurrencyStore'])->name('country_currency.store');
         Route::get('/country_currency/edit/{id}', [App\Http\Controllers\ConntryController::class, 'CurrencyEdit'])->name('country_currency.edit');
         Route::post('/country_currency/update/{id}', [App\Http\Controllers\ConntryController::class, 'CurrencyUpdate'])->name('country_currency.update');
         Route::post('/country_currency/delete/{id}', [App\Http\Controllers\ConntryController::class, 'CurrencyDelete'])->name('country_currency.delete');



        // Category

        Route::get('/category', [App\Http\Controllers\CategoryController::class, 'index'])->name('category.index');
        Route::get('/category/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('category.create');
        Route::post('/category/store', [App\Http\Controllers\CategoryController::class, 'store'])->name('category.store');
        Route::get('/category/view/{id}', [App\Http\Controllers\CategoryController::class, 'view'])->name('category.view');
        Route::get('/category/edit/{id}', [App\Http\Controllers\CategoryController::class, 'edit'])->name('category.edit');
        Route::post('/category/update/{id}', [App\Http\Controllers\CategoryController::class, 'update'])->name('category.update');
        Route::post('/category/delete/{id}', [App\Http\Controllers\CategoryController::class, 'delete'])->name('category.delete');
        Route::get('/category/expire/edit/{id}', [App\Http\Controllers\CategoryController::class, 'editExpire'])->name('category.expire');
        Route::post('/category/expiry/update/{id}', [App\Http\Controllers\CategoryController::class, 'updateExpiry'])->name('category.expiry.update');

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
        Route::post('/custom_field/update_subcategory', [App\Http\Controllers\CustomFieldController::class, 'updateSubcategory'])->name('custom_field.update_subcategory');
        Route::delete('/custom_field/category_field_delete/{id}', [App\Http\Controllers\CustomFieldController::class, 'CategoryFieldDelete'])->name('custom_field.category_field_delete');
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
        Route::get('/ad/job/request/list', [App\Http\Controllers\AdsController::class, 'jobRequest'])->name('job.index');
        Route::get('/ad/job/request/documents/{id}', [App\Http\Controllers\AdsController::class, 'jobRequestDocs'])->name('job.documents');

        Route::get('/get/custom/field', [App\Http\Controllers\AdsController::class, 'getCustomField'])->name('ad.get.custom_field');
        Route::get('/get/master/dependency', [App\Http\Controllers\AdsController::class, 'getMasterDependency'])->name('ad.get.master.dependency');
        Route::get('/ads/related/field', [App\Http\Controllers\AdsController::class, 'getAdsRelated']);
        Route::get('/get/motor/feature', [App\Http\Controllers\AdsController::class, 'getMotorFeature']);


        //ad inactive

        Route::get('/ad_inactive/list', [App\Http\Controllers\AdsController::class, 'InactiveIndex'])->name('ads.inactive');
        Route::post('/ad_inactive/accept/{id}', [App\Http\Controllers\AdsController::class, 'adReAccept'])->name('ad.inactive.accept');
        Route::get('/ad_inactive/view/{id}', [App\Http\Controllers\AdsController::class, 'adsInactiveView'])->name('ad.inactive.view');

        // Ads Request

        Route::get('/ad_request', [App\Http\Controllers\AdsController::class, 'adRequestIndex'])->name('ad_request.index');
        Route::get('/ad_request/details/{id}', [App\Http\Controllers\AdsController::class, 'adRequestDetails'])->name('ad_request.details');
        Route::post('/ad/accept/{id}', [App\Http\Controllers\AdsController::class, 'adAccept'])->name('ad.accept');
        Route::get('/ad_request/document/{id}', [App\Http\Controllers\AdsController::class, 'adRequestDocument'])->name('ad_request.document');
        Route::post('/ad/user/wallet', [App\Http\Controllers\AdsController::class, 'walletAdd'])->name('user.add.wallet');

        Route::get('/get/reject/reson', [App\Http\Controllers\AdsController::class, 'getRejectReson']);
        Route::post('/ad/reject', [App\Http\Controllers\AdsController::class, 'adReject'])->name('reject.ads');
        Route::post('/ad/refund', [App\Http\Controllers\AdsController::class, 'adRefund'])->name('refund.ads');

        // Banner

        Route::get('/banner', [App\Http\Controllers\BannerController::class, 'index'])->name('banner.index');
        Route::post('/banner/store', [App\Http\Controllers\BannerController::class, 'store'])->name('banner.store');
        Route::get('/banner/view/{id}', [App\Http\Controllers\BannerController::class, 'view'])->name('banner.view');
        Route::post('/banner/update', [App\Http\Controllers\BannerController::class, 'update'])->name('banner.update');
        Route::post('/banner/delete/{id}', [App\Http\Controllers\BannerController::class, 'delete'])->name('banner.delete');

          // App Banner

          Route::get('/appbanner', [App\Http\Controllers\AppBannerController::class, 'index'])->name('appbanner.index');
          Route::post('/appbanner/store', [App\Http\Controllers\AppBannerController::class, 'store'])->name('appbanner.store');
          Route::get('/appbanner/view/{id}', [App\Http\Controllers\AppBannerController::class, 'view'])->name('appbanner.view');
          Route::post('/appbanner/update', [App\Http\Controllers\AppBannerController::class, 'update'])->name('appbanner.update');
  
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
        Route::get('/testimonial/delete/{id}', [App\Http\Controllers\TestimonialController::class, 'delete'])->name('testimonial.delete');


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
        
       // Ads enquiry

        Route::get('/enquiry', [App\Http\Controllers\EnquiryController::class, 'index'])->name('enquiry.index');
        Route::get('/enquiry/view/{id}', [App\Http\Controllers\EnquiryController::class, 'show'])->name('enquiry.view');
        
        Route::get('/get/notification', [App\Http\Controllers\AdsController::class, 'adNotification']);
        Route::post('/read/notification', [App\Http\Controllers\AdsController::class, 'readNotification']);

        Route::post('/upload/document/{id}', [App\Http\Controllers\PaymentController::class, 'documentUpload'])->name('payment.document.upload');

        Route::get('/admin/logout', function () {
            Auth::logout();

            return redirect()->route('login.index');
        })->name('logout');
    });
});

// Route::view('/test', 'enquiry_replay');
