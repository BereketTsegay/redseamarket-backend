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


Route::view('users', 'user.user_list')->name('user.index');

Route::view('/ad_request', 'ads.request.ad_request')->name('ad_request.index');
Route::view('/ad_request/details', 'ads.request.request_details')->name('ad_request.details');

Route::get('/', function(){
    return redirect()->route('login.index');
});
Route::get('/forgotpassword/index', [App\Http\Controllers\LoginController::class, 'forgotPasswordIndex'])->name('forgotpassword.index');
Route::post('/forgotpassword/store', [App\Http\Controllers\LoginController::class, 'forgotPasswordStore'])->name('forgotpassword.store');

// Logout user Back button Cache clearing
Route::group(['middleware' => ['revalidate']], function(){

    Route::get('/login', [App\Http\Controllers\LoginController::class, 'index'])->name('login.index');
    Route::post('/login/store', [App\Http\Controllers\LoginController::class, 'store'])->name('login.store');
    
    // Middleware Prevent Unautherized access

    Route::group(['middleware' => ['adminAuth']], function(){

        // Global

        Route::get('/global/state/get', [App\Http\Controllers\CategoryController::class, 'getState']);
        Route::get('/global/city/get', [App\Http\Controllers\CategoryController::class, 'getCity']);

        Route::post('/admin/change/password', [App\Http\Controllers\LoginController::class, 'changePassword'])->name('admin.change.password');
        Route::get('/admin/profile', [App\Http\Controllers\LoginController::class, 'profile'])->name('admin.profile');
        Route::get('/admin/profile/edit/{id}', [App\Http\Controllers\LoginController::class, 'profileEdit'])->name('admin.profile.edit');
        Route::post('/admin/profile/update/{id}', [App\Http\Controllers\LoginController::class, 'profileUpdate'])->name('admin.profile.update');

        Route::get('dashboard', [App\Http\Controllers\LoginController::class, 'dashboard'])->name('dashboard');


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

        Route::get('/get/custom/field', [App\Http\Controllers\AdsController::class, 'getCustomField'])->name('ad.get.custom_field');

        Route::get('/admin/logout', function(){
            Auth::logout();
    
            return redirect()->route('login.index');
        })->name('logout');
    });
    
});