<?php

namespace App\Http\Controllers;

use App\Models\Auth;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::with('merchant');

        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        $auths = $query->orderBy('created_at', 'desc')->paginate(15);
        $merchants = Merchant::active()->get();

        return view('auths.index', compact('auths', 'merchants'));
    }

    public function create()
    {
        $merchants = Merchant::active()->get();
        return view('auths.create', compact('merchants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:tb_auths,code',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6|max:100',
            'merchant_id' => 'nullable|exists:tb_merchants,id',
            'active' => 'boolean',
        ]);

        Auth::create($request->all());

        return redirect()->route('auths.index')
            ->with('success', 'Authentification créée avec succès.');
    }

    public function show(Auth $auth)
    {
        $auth->load(['merchant', 'authTokens', 'messages']);
        return view('auths.show', compact('auth'));
    }

    public function edit(Auth $auth)
    {
        $merchants = Merchant::active()->get();
        return view('auths.edit', compact('auth', 'merchants'));
    }

    public function update(Request $request, Auth $auth)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:tb_auths,code,' . $auth->id,
            'username' => 'nullable|string|max:100',
            'merchant_id' => 'nullable|exists:tb_merchants,id',
            'active' => 'boolean',
        ]);

        $data = $request->except('password');
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6|max:100']);
            $data['password'] = $request->password;
        }

        $auth->update($data);

        return redirect()->route('auths.index')
            ->with('success', 'Authentification mise à jour avec succès.');
    }

    public function destroy(Auth $auth)
    {
        $auth->delete();

        return redirect()->route('auths.index')
            ->with('success', 'Authentification supprimée avec succès.');
    }
}