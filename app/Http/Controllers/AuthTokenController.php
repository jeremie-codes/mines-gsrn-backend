<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use App\Models\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthTokenController extends Controller
{
    public function index(Request $request)
    {
        $query = AuthToken::with('auth.merchant');

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        $tokens = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('auth-tokens.index', compact('tokens'));
    }

    public function create()
    {
        $auths = Auth::active()->with('merchant')->get();
        return view('auth-tokens.create', compact('auths'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:200',
            'token' => 'required|string',
            'auth_id' => 'required|exists:tb_auths,id',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
        ]);

        AuthToken::create($request->all());

        return redirect()->route('auth-tokens.index')
            ->with('success', 'Token créé avec succès.');
    }

    public function show(AuthToken $authToken)
    {
        $authToken->load('auth.merchant');
        return view('auth-tokens.show', compact('authToken'));
    }

    public function edit(AuthToken $authToken)
    {
        $auths = Auth::active()->with('merchant')->get();
        return view('auth-tokens.edit', compact('authToken', 'auths'));
    }

    public function update(Request $request, AuthToken $authToken)
    {
        $request->validate([
            'code' => 'required|string|max:200',
            'token' => 'required|string',
            'auth_id' => 'required|exists:tb_auths,id',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
        ]);

        $authToken->update($request->all());

        return redirect()->route('auth-tokens.index')
            ->with('success', 'Token mis à jour avec succès.');
    }

    public function destroy(AuthToken $authToken)
    {
        $authToken->delete();

        return redirect()->route('auth-tokens.index')
            ->with('success', 'Token supprimé avec succès.');
    }
}