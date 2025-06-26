@extends('layouts.app')

@section('title', 'Nouveau Message - SMS Gateway')
@section('header', 'Créer un Nouveau Message SMS')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('messages.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informations du Message</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Contenu et destinataire du message SMS.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Numéro de téléphone *</label>
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required class="px-4 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-4 py-2 border rounded-md">
                            <p class="mt-2 text-sm text-gray-500">Format international recommandé (ex: +243123456789)</p>
                            @error('phone_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="content" class="block text-sm font-medium text-gray-700">Contenu du message *</label>
                            <textarea name="content" id="content" rows="4" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-4 py-2 border rounded-md">{{ old('content') }}</textarea>
                            <p class="mt-2 text-sm text-gray-500">Contenu du message SMS à envoyer</p>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="reference" class="block text-sm font-medium text-gray-700">Référence</label>
                            <input type="text" name="reference" id="reference" value="{{ old('reference') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-4 py-2 border rounded-md">
                            <p class="mt-2 text-sm text-gray-500">Référence optionnelle pour identifier le message</p>
                            @error('reference')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Configuration d'Envoi</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Paramètres d'authentification et de configuration SMS.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="merchant_id" class="block text-sm font-medium text-gray-700">Merchant</label>
                            <select name="merchant_id" id="merchant_id" class="px-4 py-2 rounded-md px-4 py-2 border shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Sélectionner un merchant</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }} ({{ $merchant->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('merchant_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="auth_id" class="block text-sm font-medium text-gray-700">Authentification</label>
                            <select name="auth_id" id="auth_id" class="px-4 py-2 rounded-md px-4 py-2 border shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Sélectionner une authentification</option>
                                @foreach($auths as $auth)
                                    <option value="{{ $auth->id }}" {{ old('auth_id') == $auth->id ? 'selected' : '' }}>
                                        {{ $auth->code }}
                                        @if($auth->merchant)
                                            ({{ $auth->merchant->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('auth_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-3">
                            <label for="sms_from" class="block text-sm font-medium text-gray-700">SMS From</label>
                            <input type="text" name="sms_from" id="sms_from" value="{{ old('sms_from') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-4 py-2 border rounded-md">
                            @error('sms_from')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-3">
                            <label for="sms_login" class="block text-sm font-medium text-gray-700">SMS Login</label>
                            <input type="text" name="sms_login" id="sms_login" value="{{ old('sms_login') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-4 py-2 border rounded-md">
                            @error('sms_login')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('messages.index') }}" class="bg-white py-2 px-4 border px-4 py-2 border rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Annuler
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Créer le message
            </button>
        </div>
    </form>
</div>
@endsection
