<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Merchant;
use App\Models\User;
use App\Models\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_messages' => Message::count(),
            'sent_messages' => Message::sent()->count(),
            'delivered_messages' => Message::delivered()->count(),
            'failed_messages' => Message::failed()->count(),
            'active_merchants' => Merchant::active()->count(),
            'total_users' => User::enabled()->count(),
        ];

        $recent_messages = Message::with(['merchant', 'auth'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $merchants = Merchant::active()->get();

        return view('dashboard.index', compact('stats', 'recent_messages', 'merchants'));
    }
}