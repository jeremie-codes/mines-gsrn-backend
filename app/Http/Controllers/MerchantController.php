<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        $query = Merchant::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        $merchants = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('merchants.index', compact('merchants'));
    }

    public function create()
    {
        return view('merchants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:tb_merchants,code',
            'active' => 'boolean',
            'own_config' => 'boolean',
            'sms_from' => 'nullable|string|max:50',
            'sms_login' => 'nullable|string|max:50',
        ]);

        Merchant::create($request->all());

        return redirect()->route('merchants.index')
            ->with('success', 'Merchant créé avec succès.');
    }

    public function show(Merchant $merchant)
    {
        $merchant->load(['auths', 'messages']);
        return view('merchants.show', compact('merchant'));
    }

    public function edit(Merchant $merchant)
    {
        return view('merchants.edit', compact('merchant'));
    }

    public function update(Request $request, Merchant $merchant)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:tb_merchants,code,' . $merchant->id,
            'active' => 'boolean',
            'own_config' => 'boolean',
            'sms_from' => 'nullable|string|max:50',
            'sms_login' => 'nullable|string|max:50',
        ]);

        $merchant->update($request->all());

        return redirect()->route('merchants.index')
            ->with('success', 'Merchant mis à jour avec succès.');
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();

        return redirect()->route('merchants.index')
            ->with('success', 'Merchant supprimé avec succès.');
    }
}