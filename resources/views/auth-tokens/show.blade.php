@extends('layouts.app')

@section('title', 'Token: ' . $authToken->code . ' - SMS Gateway')
@section('header', 'Détails du Token d\'Authentification')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $authToken->code }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations détaillées du token d'authentification</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('auth-tokens.edit', $authToken) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Modifier
                </a>
                <a href="{{ route('auth-tokens.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Retour à la liste
                </a>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono">{{ $authToken->id }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Code</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $authToken->code }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Authentification</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($authToken->auth)
                            <a href="{{ route('auths.show', $authToken->auth) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $authToken->auth->code }}
                            </a>
                        @else
                            Aucune authentification associée
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Merchant</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($authToken->auth && $authToken->auth->merchant)
                            <a href="{{ route('merchants.show', $authToken->auth->merchant) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $authToken->auth->merchant->name }} ({{ $authToken->auth->merchant->code }})
                            </a>
                        @else
                            Aucun merchant associé
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($authToken->active && (!$authToken->expires_at || $authToken->expires_at->isFuture()))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                        @elseif($authToken->expires_at && $authToken->expires_at->isPast())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Expiré
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactif
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Date d'expiration</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($authToken->expires_at)
                            {{ $authToken->expires_at->format('d/m/Y à H:i') }}
                            @if($authToken->expires_at->isPast())
                                <span class="ml-2 text-red-600">(Expiré)</span>
                            @elseif($authToken->expires_at->diffInDays() <= 7)
                                <span class="ml-2 text-orange-600">(Expire bientôt)</span>
                            @endif
                        @else
                            Jamais
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Token</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="bg-gray-100 p-3 rounded-md">
                            <code class="text-xs break-all">{{ $authToken->token ?? 'Non défini' }}</code>
                        </div>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $authToken->created_at->format('d/m/Y à H:i') }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Modifié le</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $authToken->modified_at ? $authToken->modified_at->format('d/m/Y à H:i') : 'Jamais modifié' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection