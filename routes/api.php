<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('api')->group(function () {

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


    // Routes API pour les membres (application mobile)
    Route::get('members', [MemberController::class, 'index']);
    Route::get('members/{member}', [MemberController::class, 'show']);
    Route::post('members/create', [MemberController::class, 'apiStore']);
    Route::put('members/{id}', [MemberController::class, 'update']);
    Route::delete('members/{id}', [MemberController::class, 'destroy']);

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
    Route::put('sites/{id}', [SiteController::class, 'update'])->name('sites.update');
    Route::delete('sites/{id}', [PoolController::class, 'destroy'])->name('sites.delete');

    // Routes pour les Pools
    Route::get('pools', [PoolController::class, 'index'])->name('pools.index');
    Route::get('pools/{id}', [PoolController::class, 'show'])->name('pools.show');
    Route::post('pools/create', [PoolController::class, 'store'])->name('pools.create');
    Route::put('pools/{id}', [PoolController::class, 'update'])->name('pools.update');
    Route::delete('pools/{id}', [PoolController::class, 'destroy'])->name('pools.delete');
    Route::get('chefs/pools', [PoolController::class, 'getChefs'])->name('pools.chefs');

    // Route pour récupérer les communes par ville (AJAX)
    Route::get('townships/city/{cityId}', [MemberController::class, 'getTownshipsByCity'])->name('townships.by-city');

    // Routes pour les Utilisateurs
    Route::resource('users', UserController::class);

    Route::get('townships', [UserController::class, 'getTownship']);
    Route::get('townships/{id}', [UserController::class, 'getTownshipById']);
    Route::get('cities', [UserController::class, 'getCities']);
    Route::get('cities/{id}', [UserController::class, 'getCityById']);
    Route::get('countries', [UserController::class, 'getCountries']);
    Route::get('countries/{id}', [UserController::class, 'getCountryById']);

    Route::get('functions', [UserController::class, 'getFunction']);
    Route::get('functions/{id}', [UserController::class, 'getFunctionById']);
    Route::post('functions/create', [UserController::class, 'createFunction']);
    Route::put('functions/update', [UserController::class, 'updateFunction']);
    Route::delete('functions/delete/{id}', [UserController::class, 'deleteFunction']);

    Route::post('townships/create', [UserController::class, 'createTownship']);
    Route::post('cities/create', [UserController::class, 'createCity']);
    Route::post('countries/create', [UserController::class, 'createCountry']);
    Route::put('townships/update', [UserController::class, 'updateTownship']);
    Route::put('cities/update', [UserController::class, 'updateCity']);
    Route::put('countries/update', [UserController::class, 'updateCountry']);
    Route::delete('townships/delete/{id}', [UserController::class, 'deleteTownship']);
    Route::delete('cities/delete/{id}', [UserController::class, 'deleteCity']);
    Route::delete('countries/delete/{id}', [UserController::class, 'deleteCountry']);

});


