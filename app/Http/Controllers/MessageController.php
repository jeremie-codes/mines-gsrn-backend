<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Auth;
use App\Models\Merchant;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::with(['auth', 'merchant']);

        if ($request->filled('search')) {
            $query->where('phone_number', 'like', '%' . $request->search . '%')
                  ->orWhere('reference', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'sent':
                    $query->where('sent', true);
                    break;
                case 'delivered':
                    $query->where('delivered', true);
                    break;
                case 'failed':
                    $query->where('sent', false);
                    break;
            }
        }

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);
        $merchants = Merchant::active()->get();

        return view('messages.index', compact('messages', 'merchants'));
    }

    public function create()
    {
        $auths = Auth::active()->with('merchant')->get();
        $merchants = Merchant::active()->get();
        return view('messages.create', compact('auths', 'merchants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'reference' => 'nullable|string|max:50',
            'sms_from' => 'nullable|string|max:50',
            'sms_login' => 'nullable|string|max:50',
            'auth_id' => 'nullable|exists:tb_auths,id',
            'merchant_id' => 'nullable|exists:tb_merchants,id',
        ]);

        Message::create($request->all());

        return redirect()->route('messages.index')
            ->with('success', 'Message créé avec succès.');
    }

    public function show(Message $message)
    {
        $message->load(['auth', 'merchant']);
        return view('messages.show', compact('message'));
    }

    public function edit(Message $message)
    {
        $auths = Auth::active()->with('merchant')->get();
        $merchants = Merchant::active()->get();
        return view('messages.edit', compact('message', 'auths', 'merchants'));
    }

    public function update(Request $request, Message $message)
    {
        $request->validate([
            'content' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'reference' => 'nullable|string|max:50',
            'sms_from' => 'nullable|string|max:50',
            'sms_login' => 'nullable|string|max:50',
            'sent' => 'boolean',
            'delivered' => 'boolean',
            'closed' => 'boolean',
            'auth_id' => 'nullable|exists:tb_auths,id',
            'merchant_id' => 'nullable|exists:tb_merchants,id',
        ]);

        $message->update($request->all());

        return redirect()->route('messages.index')
            ->with('success', 'Message mis à jour avec succès.');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Message supprimé avec succès.');
    }
}