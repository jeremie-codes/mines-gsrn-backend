<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('sites.index');
});

// Routes pour les Sites
Route::resource('sites', SiteController::class);

// Routes pour les Pools
Route::resource('pools', PoolController::class);

// Routes pour les Membres
Route::resource('members', MemberController::class);

// Routes spéciales pour les membres
Route::get('members/{member}/assign-role', [MemberController::class, 'showAssignRole'])->name('members.assign-role');
Route::post('members/{member}/assign-role', [MemberController::class, 'assignRole'])->name('members.assign-role.store');
Route::get('members/{member}/create-user', [MemberController::class, 'showCreateUser'])->name('members.create-user');
Route::post('members/{member}/create-user', [MemberController::class, 'createUser'])->name('members.create-user.store');

// Route pour récupérer les communes par ville (AJAX)
Route::get('api/townships/city/{cityId}', [MemberController::class, 'getTownshipsByCity'])->name('townships.by-city');

// Routes pour les Utilisateurs
Route::resource('users', UserController::class);