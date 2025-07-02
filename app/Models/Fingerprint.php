<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PresenceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:agents,id',
        ]);

        Presence::create([
            'agent_id' => $validated['agent_id'],
        ]);

        return response()->json(['message' => 'Présence enregistrée'], 201);
    }
}
