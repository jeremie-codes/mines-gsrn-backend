@extends('layouts.app')

@section('title', 'Modifier Message: ' . $message->phone_number . ' - SMS Gateway')
@section('header', 'Modifier le Message SMS')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('messages.update', $message) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informations du Message</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Modifier le contenu et les paramètres du message SMS.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Numéro de téléphone *</label>
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $message->phone_number) }}" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-4 py-2 border rounded-md">
                            @error('phone_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="content" class="block text-sm font-medium text-gray-700">Contenu du message *</label>
                            <textarea name="content" id="content" rows="4" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border rounded-md">{{ old('content', $message->content) }}</textarea>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="reference" class="block text-sm font-medium text-gray-700">Référence</label>
                            <input type="text" name="reference" id="reference" value="{{ old('reference', $message->reference) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm px-4 py-2 border rounded-md">
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
                        Modifier les paramètres d'authentification et de configuration SMS.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="merchant_id" class="block text-sm font-medium text-gray-700">Merchant</label>
                            <select name="merchant_id" id="merchant_id" class="px-4 py-2 rounded-md border shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Sélectionner un merchant</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $message->merchant_id) == $merchant->id ? 'selected' : '' }}>
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
                            <select name="auth_id" id="auth_id" class="px-4 py-2 rounded-md border shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Sélectionner une authentification</option>
                                @foreach($auths as $auth)
                                    <option value="{{ $auth->id }}" {{ old('auth_id', $message->auth_id) == $auth->id ? 'selected' : '' }}>
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
                            <input type="text" name="sms_from" id="sms_from" value="{{ old('sms_from', $message->sms_from) }}" class="mt-1 py-2 px-4 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border rounded-md">
                            @error('sms_from')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-3">
                            <label for="sms_login" class="block text-sm font-medium text-gray-700">SMS Login</label>
                            <input type="text" name="sms_login" id="sms_login" value="{{ old('sms_login', $message->sms_login) }}" class="mt-1 py-2 px-4 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border rounded-md">
                            @error('sms_login')
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
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Statut du Message</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Modifier le statut d'envoi et de livraison du message.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="sent" name="sent" type="checkbox" value="1" {{ old('sent', $message->sent) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="sent" class="font-medium text-gray-700">Message envoyé</label>
                                <p class="text-gray-500">Le message a été envoyé avec succès</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="delivered" name="delivered" type="checkbox" value="1" {{ old('delivered', $message->delivered) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="delivered" class="font-medium text-gray-700">Message livré</label>
                                <p class="text-gray-500">Le message a été livré au destinataire</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="closed" name="closed" type="checkbox" value="1" {{ old('closed', $message->closed) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="closed" class="font-medium text-gray-700">Message fermé</label>
                                <p class="text-gray-500">Le traitement du message est terminé</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('messages.show', $message) }}" class="bg-white py-2 px-4 border border rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Annuler
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
