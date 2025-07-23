<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CotisationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\PoolController;
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
    Route::post('flexpaie_callback', [CotisationController::class, 'callback'])->name('callbak');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('flexpaie/{id}', [CotisationController::class, 'flexpaie'])->name('flexpaie');

        Route::post('logout', [UserController::class, 'logout'])->name('logout');

        Route::post('/profiles/{id}', [RoleController::class, 'update']);
        Route::resource('users', UserController::class);

        Route::get('/profile-image/{filename}', function ($filename) {

            // $filename = preg_replace('#^profiles/#', '', $filename);

            $path = public_path('storage/' . $filename);

            if (!file_exists($path)) {
                abort(404);
            }

            $file = file_get_contents($path);
            $type = mime_content_type($path);

            return response($file, 200)
                ->header('Content-Type', $type)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Origin', '*');
        })->where('filename', '.+');

        Route::get('/carte/preview/{id}', [PdfController::class, 'previewCarte'])->name('carte.preview');
        Route::post('/carte/generate-pdf', [PdfController::class, 'generatePDF'])->name('carte.pdf.generate');

        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::post('categories/update/{id}', [CategoryController::class, 'update']);
        Route::post('categories/{id}', [CategoryController::class, 'destroy']);

        Route::get('cotisations', [CotisationController::class, 'index']);
        // Route::get('cotisations/{member}', [CotisationController::class, 'show']);
        Route::post('cotisations/{id}', [CotisationController::class, 'store']);
        Route::post('cotisations/update/{id}', [CotisationController::class, 'update']);
        Route::post('cotisations/{id}', [CotisationController::class, 'destroy']);

        // Routes API pour les membres (application mobile)
        Route::get('members/export', [MemberController::class, 'export']);
        Route::get('members', [MemberController::class, 'index']);
        Route::get('members/{member}', [MemberController::class, 'show']);
        Route::post('members/create', [MemberController::class, 'store']);
        Route::post('members/update', [MemberController::class, 'update']);
        Route::post('members/{id}', [MemberController::class, 'destroy']);

        // Route::resource('members', MemberController::class);
        // Routes spéciales pour les membres
        Route::get('members/{member}/assign-role', [MemberController::class, 'showAssignRole'])->name('members.assign-role');
        Route::post('members/{member}/assign-role', [MemberController::class, 'assignRole'])->name('members.assign-role.store');
        Route::get('members/{member}/create-user', [MemberController::class, 'showCreateUser'])->name('members.create-user');
        Route::post('members/{member}/create-user', [MemberController::class, 'createUser'])->name('members.create-user.store');

        // Routes pour les Sites
        // Route::resource('sites', SiteController::class);
        Route::get('sites', [SiteController::class, 'index'])->name('sites.index');
        Route::get('sites/{id}', [SiteController::class, 'show'])->name('sites.show');
        Route::post('sites/create', [SiteController::class, 'store'])->name('sites.create');
        Route::post('sites/update/{id}', [SiteController::class, 'update'])->name('sites.update');
        Route::post('sites/{id}', [SiteController::class, 'destroy'])->name('sites.delete');

        // Routes pour les Pools
        Route::get('pools', [PoolController::class, 'index'])->name('pools.index');
        Route::get('pools/{id}', [PoolController::class, 'show'])->name('pools.show');
        Route::post('pools/create', [PoolController::class, 'store'])->name('pools.create');
        Route::post('pools/update/{id}', [PoolController::class, 'update'])->name('pools.update');
        Route::post('pools/{id}', [PoolController::class, 'destroy'])->name('pools.delete');

        Route::get('chefs/pools', [PoolController::class, 'getChefs'])->name('pools.chefs');
        Route::get('chefs/pools/{id}', [PoolController::class, 'getChefByPoolId']);

    });
    // Routes Public API pour les utilisateurs
    // Route pour récupérer les communes par ville (AJAX)
    Route::get('townships/city/{cityId}', [MemberController::class, 'getTownshipsByCity'])->name('townships.by-city');

    Route::get('townships', [UserController::class, 'getTownship']);
    Route::get('townships/{id}', [UserController::class, 'getTownshipById']);
    Route::get('cities', [UserController::class, 'getCities']);
    Route::get('cities/{id}', [UserController::class, 'getCityById']);
    Route::get('countries', [UserController::class, 'getCountries']);
    Route::get('countries/{id}', [UserController::class, 'getCountryById']);

    Route::get('functions', [UserController::class, 'getFunction']);
    Route::get('functions/{id}', [UserController::class, 'getFunctionById']);
    Route::post('functions/create', [UserController::class, 'createFunction']);
    Route::post('functions/update/{id}', [UserController::class, 'updateFunction']);
    Route::post('functions/{id}', [UserController::class, 'deleteFunction']);

    Route::post('townships/create', [UserController::class, 'createTownship']);
    Route::post('cities/create', [UserController::class, 'createCity']);
    Route::post('countries/create', [UserController::class, 'createCountry']);
    Route::post('townships/update/{id}', [UserController::class, 'updateTownship']);
    Route::post('cities/update/{id}', [UserController::class, 'updateCity']);
    Route::post('countries/update/{id}', [UserController::class, 'updateCountry']);
    Route::post('townships/{id}', [UserController::class, 'deleteTownship']);
    Route::post('cities/{id}', [UserController::class, 'deleteCity']);
    Route::post('countries/{id}', [UserController::class, 'deleteCountry']);

    // Mobile Route
    Route::post('app/members/create', [MemberController::class, 'apiStore']);
    Route::get('app/sites', [SiteController::class, 'index']);
    Route::get('app/pools', [PoolController::class, 'index']);
    Route::get('app/chefs/pools', [PoolController::class, 'getChefsApp']);

});


