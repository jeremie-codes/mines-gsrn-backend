<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agent;

class FingerprintController extends Controller
{
    public function index()
    {
        $agents = Agent::whereNotNull('fingerprint_image')->get();

        $data = $agents->map(function ($agent) {
            return [
                'agent_id' => $agent->id,
                'image_url' => url('storage/' . $agent->fingerprint_image),
            ];
        });

        return response()->json($data);
    }
}
