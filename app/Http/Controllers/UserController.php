<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('enabled')) {
            $query->where('enabled', $request->enabled);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:tb_users,username',
            'email' => 'nullable|email|max:50',
            'password' => 'required|string|min:6|max:100',
            'phone_number' => 'nullable|string|max:20',
            'enabled' => 'boolean',
        ]);

        $data = $request->all();
        $data['code'] = 'USER_' . strtoupper(Str::random(8));

        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:tb_users,username,' . $user->id,
            'email' => 'nullable|email|max:50',
            'phone_number' => 'nullable|string|max:20',
            'enabled' => 'boolean',
        ]);

        $data = $request->except('password');
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6|max:100']);
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}