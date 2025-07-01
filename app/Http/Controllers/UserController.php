<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
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
            $users = User::with('member', 'role')->paginate(10);

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

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function getTownship()
    {

        try {
            $townshps = Township::with('city')->get();

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
            $cities = City::with('country', 'townships')->get();

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
            $countries = Country::with('cities')->get();

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
}
