@extends('layouts.app')

@section('title', 'Modifier Configuration: ' . $configuration->code . ' - SMS Gateway')
@section('header', 'Modifier la Configuration SMS')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('configurations.update', $configuration) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informations de Base</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Modifier les paramètres généraux de la configuration SMS.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="code" class="block text-sm font-medium text-gray-700">Code *</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="code" id="code" value="{{ old('code', $configuration->code) }}" required class="border px-4 py-2 focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300">
                                <button type="button" onclick="generateCode('code', 'CONFIG_')" class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
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
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="active" name="active" type="checkbox" value="1" {{ old('active', $configuration->active) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="active" class="font-medium text-gray-700">Configuration active</label>
                                    <p class="text-gray-500">Cette configuration peut être utilisée</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Paramètres SMS</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Modifier la configuration des paramètres de connexion SMS.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-3">
                            <label for="sms_from" class="block text-sm font-medium text-gray-700">SMS From</label>
                            <input type="text" name="sms_from" id="sms_from" value="{{ old('sms_from', $configuration->sms_from) }}" class="border  px-4 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('sms_from')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-3">
                            <label for="sms_login" class="block text-sm font-medium text-gray-700">SMS Login</label>
                            <input type="text" name="sms_login" id="sms_login" value="{{ old('sms_login', $configuration->sms_login) }}" class="border  px-4 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('sms_login')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="sms_url" class="block text-sm font-medium text-gray-700">URL SMS</label>
                            <input type="url" name="sms_url" id="sms_url" value="{{ old('sms_url', $configuration->sms_url) }}" class="border  px-4 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-2 text-sm text-gray-500">URL de l'API SMS pour l'envoi</p>
                            @error('sms_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-6">
                            <label for="sms_url_check" class="block text-sm font-medium text-gray-700">URL de Vérification SMS</label>
                            <input type="url" name="sms_url_check" id="sms_url_check" value="{{ old('sms_url_check', $configuration->sms_url_check) }}" class="border  px-4 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-2 text-sm text-gray-500">URL de l'API SMS pour vérifier le statut</p>
                            @error('sms_url_check')
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
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Paramètres de Planification</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Modifier la configuration des paramètres de planification des envois.
                    </p>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-3">
                            <label for="schedule_date_format" class="block text-sm font-medium text-gray-700">Format de Date</label>
                            <input type="text" name="schedule_date_format" id="schedule_date_format" value="{{ old('schedule_date_format', $configuration->schedule_date_format) }}" placeholder="Y-m-d H:i:s" class="border  px-4 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('schedule_date_format')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-3">
                            <label for="schedule_date_value" class="block text-sm font-medium text-gray-700">Valeur de Date</label>
                            <input type="text" name="schedule_date_value" id="schedule_date_value" value="{{ old('schedule_date_value', $configuration->schedule_date_value) }}" class="border  px-4 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('schedule_date_value')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('configurations.show', $configuration) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
    for (let i = 0; i < 8; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    document.getElementById(fieldId).value = result;
}
</script>
@endsection
