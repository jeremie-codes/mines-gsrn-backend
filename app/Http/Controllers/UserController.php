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

class UserController extends Controller
{
    public function index()
    {

        try {
            $users = User::paginate(10);

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

    public function create()
    {

        try {
            $members = Member::doesntHave('user')->get();
            $roles = Role::active()->get();

            return response()->json([
                'success' => true,
                'members' => $members,
                'roles' => $roles
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'member_id' => 'required|exists:members,id|unique:users,member_id',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:8',
                'role_id' => 'required|exists:roles,id',
                'is_active' => 'boolean|default:true'
            ]);

            $user = User::create([
                'member_id' => $request->member_id,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'is_active' => $request->is_active ?? true
            ]);

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
            $user = User::with('member', 'role')->findOrFail($id);
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

    public function update(Request $request)
    {

        try {
           $request->validate([
                'user_id' => 'required|exists:users,id',
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'username' => 'required|string|unique:users,username,' .  $request->user_id,
                'password' => 'nullable|string|min:8',
                'role_id' => 'required|exists:roles,id',
                'is_active' => 'boolean'
            ]);

            $user = User::findOrFail($request->user_id);

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
            $functions = Fonction::all();

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

        try {
            $request->validate([
                'name' => 'required|string|unique:fonctions,name'
            ]);

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
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function updateFunction(Request $request)
    {

        try {
            $request->validate([
                'function_id' => 'required|exists:fonctions,id',
                'name' => 'required|string|unique:fonctions,name,' . $request->function_id
            ]);

            $function = Fonction::findOrFail($request->function_id);

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
            $townshps = Township::all();

            return response()->json([
                'success' => true,
                'townshps' => $townshps
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
            $townshp = Township::with('city')->findOrFail($id);

            if (!$townshp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commune non trouvée.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'townshp' => $townshp
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
            $cities = City::all();

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

        try {
            $request->validate([
                'name' => 'required|string|unique:countries,name',
                'code' => 'required|string|unique:countries,code',
                'is_active' => 'boolean|default:true'
            ]);

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
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function createCity(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|unique:cities,name',
                'country_id' => 'required|exists:countries,id',
                'is_active' => 'boolean|default:true'
            ]);

            $city = City::create([
                'name' => $request->name,
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
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function createTownship(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|unique:townships,name',
                'city_id' => 'required|exists:cities,id',
                'is_active' => 'boolean|default:true'
            ]);

            $township = Township::create([
                'name' => $request->name,
                'city_id' => $request->city_id,
                'is_active' => $request->is_active ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Commune créée avec succès.',
                'township' => $township
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Erreur, " .$th->getMessage()
            ], 500);
        }
    }

    public function updateCountry(Request $request)
    {

        try {
            $request->validate([
                'country_id' => 'required|exists:countries,id',
                'name' => 'required|string|unique:countries,name,' . $request->country_id,
                'code' => 'required|string|unique:countries,code,' . $request->country_id,
                'is_active' => 'boolean'
            ]);

            $country = Country::findOrFail($request->country_id);

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

    public function updateCity(Request $request)
    {

        try {
            $request->validate([
                'city_id' => 'required|exists:cities,id',
                'name' => 'required|string|unique:cities,name,' . $request->city_id,
                'country_id' => 'required|exists:countries,id',
                'is_active' => 'boolean'
            ]);

            $city = City::findOrFail($request->city_id);

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

    public function updateTownship(Request $request)
    {

        try {
            $request->validate([
                'township_id' => 'required|exists:townships,id',
                'name' => 'required|string|unique:townships,name,' . $request->township_id,
                'city_id' => 'required|exists:cities,id',
                'is_active' => 'boolean'
            ]);

            $township = Township::findOrFail($request->township_id);

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
