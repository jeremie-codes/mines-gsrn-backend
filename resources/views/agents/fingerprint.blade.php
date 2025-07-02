@extends('layouts.app')

@section('content')
<div class="container max-w-md mx-auto p-4 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Uploader empreinte pour {{ $agent->firstname }}</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('agents.fingerprint.update', $agent) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="fingerprint" class="block font-medium mb-1">Image empreinte (jpeg/png)</label>
            <input type="file" name="fingerprint" id="fingerprint" accept="image/*" required>
            @error('fingerprint')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($agent->fingerprint_image)
            <div class="mb-4">
                <p class="font-medium mb-1">Empreinte actuelle :</p>
                <img src="{{ asset('storage/' . $agent->fingerprint_image) }}" alt="Empreinte de {{ $agent->nom }}" class="max-w-xs border rounded">
            </div>
        @endif

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Enregistrer l'empreinte
        </button>
    </form>
</div>
@endsection
