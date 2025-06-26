<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $query = Configuration::query();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        $configurations = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('configurations.index', compact('configurations'));
    }

    public function create()
    {
        return view('configurations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:tb_configurations,code',
            'active' => 'boolean',
            'schedule_date_format' => 'nullable|string|max:100',
            'schedule_date_value' => 'nullable|string|max:100',
            'sms_from' => 'nullable|string|max:50',
            'sms_login' => 'nullable|string|max:50',
            'sms_url' => 'nullable|url|max:200',
            'sms_url_check' => 'nullable|url|max:200',
        ]);

        Configuration::create($request->all());

        return redirect()->route('configurations.index')
            ->with('success', 'Configuration créée avec succès.');
    }

    public function show(Configuration $configuration)
    {
        return view('configurations.show', compact('configuration'));
    }

    public function edit(Configuration $configuration)
    {
        return view('configurations.edit', compact('configuration'));
    }

    public function update(Request $request, Configuration $configuration)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:tb_configurations,code,' . $configuration->id,
            'active' => 'boolean',
            'schedule_date_format' => 'nullable|string|max:100',
            'schedule_date_value' => 'nullable|string|max:100',
            'sms_from' => 'nullable|string|max:50',
            'sms_login' => 'nullable|string|max:50',
            'sms_url' => 'nullable|url|max:200',
            'sms_url_check' => 'nullable|url|max:200',
        ]);

        $configuration->update($request->all());

        return redirect()->route('configurations.index')
            ->with('success', 'Configuration mise à jour avec succès.');
    }

    public function destroy(Configuration $configuration)
    {
        $configuration->delete();

        return redirect()->route('configurations.index')
            ->with('success', 'Configuration supprimée avec succès.');
    }
}