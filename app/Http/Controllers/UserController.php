<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Fonction;
use App\Models\User;
use App\Models\Member;
use App\Models\Role;
use App\Models\Township;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {

        try {
            $users = User::orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'users' => $users
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'nullable|string',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Les identifiants sont incorrects.',
            ], 404);
        }

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucun utilisateur authentifié ou token invalide.',
        ], 401);
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'email' => 'nullable|email|unique:users,email',
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès.',
                'user' => $user
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {

        try {
            $user = User::with('member', 'role', 'profiles')->findOrFail($id);
            // $user->load('member', 'role.permissions');

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {

        try {
            $user = User::with('member', 'role')->findOrFail($id);
            $roles = Role::active()->get();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => $user,
                'roles' => $roles
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {
           $request->validate([
                'email' => 'nullable|email|unique:users,email,' . $id,
                'username' => 'nullable|string|unique:users,username,' .  $id,
                'password' => 'nullable|string|min:8',
                'role_id' => 'nullable|exists:roles,id',
                'is_active' => 'nullable|boolean'
            ]);

            $user = User::findOrFail($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            $data = $request->all();

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès.',
                'user' => $user
            ], 200);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if (!$user) {
                return redirect()->route('users.index')
                    ->with('error', 'Utilisateur non trouvé.');
            }

            // Optionally, you can delete the associated member if needed
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function getFunction()
    {

        try {
            $functions = Fonction::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'functions' => $functions
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function getFunctionById($id)
    {

        try {
            $function = Fonction::findOrFail($id);

            if (!$function) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fonction non trouvée.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'function' => $function
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function createFunction(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        // Vérifie si une fonction avec le même nom existe déjà
        $existing = Fonction::where('name', $request->name)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Cette fonction existe déjà.',
                'function' => $existing
            ], 409); // 409 Conflict
        }

        try {
            $function = Fonction::create([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fonction créée avec succès.',
                'function' => $function
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur : " . $th->getMessage()
            ], 500);
        }
    }


    public function updateFunction(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|string|unique:fonctions,name,' . $id
            ]);

            $function = Fonction::findOrFail($id);

            if (!$function) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fonction non trouvée.'
                ], 404);
            }

            $function->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Fonction mise à jour avec succès.',
                'function' => $function
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function deleteFunction($id)
    {
        try {
            $function = Fonction::findOrFail($id);
            $function->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fonction supprimée avec succès.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function getTownship()
    {

        try {
            $townships = Township::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'townships' => $townships
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function getTownshipById($id)
    {

        try {
            $township = Township::with('city')->findOrFail($id);

            if (!$township) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commune non trouvée.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'township' => $township
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function getCities()
    {

        try {
            $cities = City::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'cities' => $cities
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }


    public function getCityById($id)
    {

        try {
            $city = City::with('country', 'townships')->findOrFail($id);

            if (!$city) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ville non trouvée.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'city' => $city
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function getCountries()
    {

        try {
            $countries = Country::all();

            return response()->json([
                'success' => true,
                'countries' => $countries
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }


    public function getCountryById($id)
    {

        try {
            $contry = Country::with('cities')->findOrFail($id);

            if (!$contry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pays non trouvé.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'contry' => $contry
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function createCountry(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'is_active' => 'nullable|boolean'
        ]);

        // Vérifie si un pays avec le même nom existe déjà
        $existingByName = Country::where('name', $request->name)->first();
        if ($existingByName) {
            return response()->json([
                'success' => false,
                'message' => 'Un pays avec ce nom existe déjà.',
                'country' => $existingByName
            ], 409);
        }

        // Vérifie si un pays avec le même code existe déjà
        $existingByCode = Country::where('code', $request->code)->first();
        if ($existingByCode) {
            return response()->json([
                'success' => false,
                'message' => 'Un pays avec ce code existe déjà.',
                'country' => $existingByCode
            ], 409);
        }

        try {
            $country = Country::create([
                'name' => $request->name,
                'code' => $request->code,
                'is_active' => $request->is_active ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pays créé avec succès.',
                'country' => $country
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur : " . $th->getMessage()
            ], 500);
        }
    }

    public function createCity(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'code' => 'required|string',
                'country_id' => 'required|exists:countries,id',
                'is_active' => 'nullable|boolean'
            ]);

            // Vérifie si une ville avec le même nom existe déjà dans le même pays
            $existingCity = City::where('name', $request->name)
                ->where('country_id', $request->country_id)
                ->first();

            if ($existingCity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette ville existe déjà dans ce pays.',
                    'city' => $existingCity
                ], 409);
            }

            $city = City::create([
                'name' => $request->name,
                'code' => $request->code,
                'country_id' => $request->country_id,
                'is_active' => $request->is_active ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ville créée avec succès.',
                'city' => $city
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur : " . $th->getMessage()
            ], 500);
        }
    }

    public function createTownship(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'code' => 'required|string',
                'city_id' => 'required|exists:cities,id',
                'is_active' => 'nullable|boolean'
            ]);

            // Vérifie si une commune avec le même nom existe déjà dans cette ville
            $existingTownship = Township::where('name', $request->name)
                ->where('city_id', $request->city_id)
                ->first();

            if ($existingTownship) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette commune existe déjà dans cette ville.',
                ], 409);
            }

            $township = Township::create([
                'name' => $request->name,
                'code' => $request->code,
                'city_id' => $request->city_id,
                'is_active' => $request->is_active ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commune créée avec succès.',
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur : " . $th->getMessage()
            ], 500);
        }
    }

    public function updateCountry(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|string|unique:countries,name,' . $id,
                'code' => 'required|string|unique:countries,code,' . $id,
                'is_active' => 'nullable|boolean'
            ]);

            $country = Country::findOrFail($id);

            if (!$country) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pays non trouvé.'
                ], 404);
            }

            $data = $request->all();
            $country->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Pays mis à jour avec succès.',
                'country' => $country
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function updateCity(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|string|unique:cities,name,' . $id,
                'country_id' => 'required|exists:countries,id',
                'is_active' => 'nullable|boolean'
            ]);

            $city = City::findOrFail($id);

            if (!$city) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ville non trouvée.'
                ], 404);
            }

            $data = $request->all();
            $city->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Ville mise à jour avec succès.',
                'city' => $city
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function updateTownship(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|string|unique:townships,name,' . $id,
                'city_id' => 'required|exists:cities,id',
                'is_active' => 'nullable|boolean'
            ]);

            $township = Township::findOrFail($id);

            if (!$township) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commune non trouvée.'
                ], 404);
            }

            $data = $request->all();
            $township->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Commune mise à jour avec succès.',
                'township' => $township
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function deleteCountry($id)
    {
        try {
            $country = Country::findOrFail($id);
            $country->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pays supprimé avec succès.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function deleteCity($id)
    {
        try {
            $city = City::findOrFail($id);
            $city->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ville supprimée avec succès.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function deleteTownship($id)
    {
        try {
            $township = Township::findOrFail($id);
            $township->delete();

            return response()->json([
                'success' => true,
                'message' => 'Commune supprimée avec succès.'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

}
