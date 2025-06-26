@extends('layouts.app')

@section('title', 'Modifier Authentification: ' . $auth->code . ' - SMS Gateway')
@section('header', 'Modifier l\'Authentification')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('auths.update', $auth) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informations d'Authentification</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Modifier les paramètres d'authentification.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="code" class="block text-sm font-medium text-gray-700">Code *</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="code" id="code" value="{{ old('code', $auth->code) }}" required class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm px-3 py-2 border border-gray-300">
                                <button type="button" onclick="generateCode('code', 'AUTH_')" class="inline-flex items-center px-3 py-2 border border-l-0 px-3 py-2 border border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </div>
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                            <input type="text" name="username" id="username" value="{{ old('username', $auth->username) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-3 py-2 border border-gray-300 rounded-md">
                            @error('username')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                            <input type="password" name="password" id="password" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-3 py-2 border border-gray-300 rounded-md">
                            <p class="mt-2 text-sm text-gray-500">Laissez vide pour conserver le mot de passe actuel</p>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="merchant_id" class="block text-sm font-medium text-gray-700">Merchant</label>
                            <select name="merchant_id" id="merchant_id" class="mt-1 block w-full rounded-md px-3 py-2 border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Sélectionner un merchant</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $auth->merchant_id) == $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }} ({{ $merchant->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('merchant_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="active" name="active" type="checkbox" value="1" {{ old('active', $auth->active) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 px-3 py-2 border border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="active" class="font-medium text-gray-700">Actif</label>
                                    <p class="text-gray-500">L'authentification peut être utilisée</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('auths.show', $auth) }}" class="bg-white py-2 px-4 border px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Annuler
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Mettre à jour
            </button>
        </div>
    </form>
</div>

<script>
function generateCode(fieldId, prefix) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = prefix;
    for (let i = 0; i < 10; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById(fieldId).value = result;
}
</script>
@endsection
