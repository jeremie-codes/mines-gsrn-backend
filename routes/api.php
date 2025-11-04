<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CotisationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('api')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::post('register/{id}', [UserController::class, 'register'])->name('register');
        Route::post('login', [UserController::class, 'login'])->name('login');
    });

    Route::get('stats', [MemberController::class, 'stats']);

    Route::middleware('auth:sanctum')->group(function () {
        // Routes API pour les membres (application mobile)
        Route::get('members', [MemberController::class, 'index']);
        Route::get('members/{member}', [MemberController::class, 'show']);
        Route::post('members/create', [MemberController::class, 'store']);
        Route::get('members/export', [MemberController::class, 'export']);
        Route::post('members/update', [MemberController::class, 'update']);
        Route::post('members/{id}', [MemberController::class, 'destroy']);

        // Routes pour les Sites
        Route::get('sites', [SiteController::class, 'index'])->name('sites.index');
        Route::get('sites/{id}', [SiteController::class, 'show'])->name('sites.show');
        Route::post('sites/create', [SiteController::class, 'store'])->name('sites.create');
        Route::post('sites/update/{id}', [SiteController::class, 'update'])->name('sites.update');
        Route::post('sites/{id}', [SiteController::class, 'destroy'])->name('sites.delete');

        // Routes pour les organization
        Route::get('organizations', [OrganizationController::class, 'index'])->name('organizations.index');
        Route::get('organization/{id}', [OrganizationController::class, 'show'])->name('organizations.show');
        Route::post('organization/create', [OrganizationController::class, 'store'])->name('organizations.create');
        Route::post('organization/update/{id}', [OrganizationController::class, 'update'])->name('organizations.update');
        Route::post('organization/{id}', [OrganizationController::class, 'destroy'])->name('organizations.delete');

        Route::post('logout', [UserController::class, 'logout'])->name('logout');
    });

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::post('users/create', [UserController::class, 'store']);
    Route::post('users/update/{id}', [UserController::class, 'update']);

    // Route pour récupérer les communes par ville (AJAX)
    Route::get('townships/city/{cityId}', [MemberController::class, 'getTownshipsByCity'])->name('townships.by-city');

    Route::get('townships', [UserController::class, 'getTownship']);
    Route::get('townships/{id}', [UserController::class, 'getTownshipById']);
    Route::get('cities', [UserController::class, 'getCities']);
    Route::get('cities/{id}', [UserController::class, 'getCityById']);
    Route::get('countries', [UserController::class, 'getCountries']);
    Route::get('countries/{id}', [UserController::class, 'getCountryById']);

    Route::post('townships/create', [UserController::class, 'createTownship']);
    Route::post('cities/create', [UserController::class, 'createCity']);
    Route::post('countries/create', [UserController::class, 'createCountry']);
    Route::post('townships/update/{id}', [UserController::class, 'updateTownship']);
    Route::post('cities/update/{id}', [UserController::class, 'updateCity']);
    Route::post('countries/update/{id}', [UserController::class, 'updateCountry']);
    Route::post('townships/{id}', [UserController::class, 'deleteTownship']);
    Route::post('cities/{id}', [UserController::class, 'deleteCity']);
    Route::post('countries/{id}', [UserController::class, 'deleteCountry']);

    // Mobile Route Apis
    Route::post('app/members/create', [MemberController::class, 'apiStore']);
    Route::get('app/sites', [SiteController::class, 'appIndex']);
    Route::get('app/organizations', [OrganizationController::class, 'indexApi']);

});


