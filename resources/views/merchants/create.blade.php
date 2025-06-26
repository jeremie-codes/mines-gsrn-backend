@extends('layouts.app')

@section('title', 'Nouveau Merchant - SMS Gateway')
@section('header', 'Créer un Nouveau Merchant')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('merchants.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informations du Merchant</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Informations de base du merchant et configuration SMS.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-3 py-2 border border-gray-300 rounded-md">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="Généré automatiquement si vide" class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm px-3 py-2 border border-gray-300">
                                <button type="button" onclick="generateCode('code', 'MERCHANT_')" class="inline-flex items-center px-3 py-2 border border-l-0 px-3 py-2 border border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Code unique pour identifier le merchant (généré automatiquement si vide)</p>
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-3">
                            <label for="sms_from" class="block text-sm font-medium text-gray-700">SMS From</label>
                            <input type="text" name="sms_from" id="sms_from" value="{{ old('sms_from') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-3 py-2 border border-gray-300 rounded-md">
                            @error('sms_from')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-3">
                            <label for="sms_login" class="block text-sm font-medium text-gray-700">SMS Login</label>
                            <input type="text" name="sms_login" id="sms_login" value="{{ old('sms_login') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-3 py-2 border border-gray-300 rounded-md">
                            @error('sms_login')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="active" name="active" type="checkbox" value="1" {{ old('active') ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 px-3 py-2 border border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="active" class="font-medium text-gray-700">Actif</label>
                                    <p class="text-gray-500">Le merchant peut utiliser la plateforme</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="own_config" name="own_config" type="checkbox" value="1" {{ old('own_config') ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 px-3 py-2 border border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="own_config" class="font-medium text-gray-700">Configuration propre</label>
                                    <p class="text-gray-500">Le merchant utilise sa propre configuration SMS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('merchants.index') }}" class="bg-white py-2 px-4 border px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Annuler
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Créer le merchant
            </button>
        </div>
    </form>
</div>

<script>
function generateCode(fieldId, prefix) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = prefix;
    for (let i = 0; i < 8; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById(fieldId).value = result;
}
</script>
@endsection
