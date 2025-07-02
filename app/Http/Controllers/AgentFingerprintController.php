<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgentFingerprintController extends Controller
{
    public function edit(Member $agent)
    {
        return view('agents.fingerprint', compact('agent'));
    }

    public function update(Request $request, Member $agent)
    {
        $request->validate([
            'fingerprint' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('fingerprint')) {
            // Supprime ancienne image si existe
            if ($agent->fingerprint_image) {
                Storage::disk('public')->delete($agent->fingerprint_image);
            }

            // Stocke nouvelle image
            $path = $request->file('fingerprint')->store('fingerprints', 'public');

            // Enregistre le chemin dans la BDD
            $agent->fingerprint_image = $path;
            $agent->save();
        }

        return redirect()->route('agents.fingerprint.edit', $agent)->with('success', 'Empreinte mise à jour avec succès.');
    }
}
