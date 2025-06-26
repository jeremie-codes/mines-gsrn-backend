@extends('layouts.app')

@section('title', 'Tokens d\'Authentification - SMS Gateway')
@section('header', 'Gestion des Tokens d\'Authentification')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700">Liste de tous les tokens d'authentification avec leurs informations et statuts.</p>
    </div>
    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <a href="{{ route('auth-tokens.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
            Nouveau Token
        </a>
    </div>
</div>

<!-- Filters -->
<div class="mt-6 bg-white shadow rounded-lg p-4">
    <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Code du token..." class="mt-1 block w-full rounded-md px-4 py-2 border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="active" class="block text-sm font-medium text-gray-700">Statut</label>
            <select name="active" id="active" class="mt-1 block w-full rounded-md px-4 py-2 border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Tous</option>
                <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Actif</option>
                <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>
        <div class="sm:col-span-2 flex items-end space-x-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Filtrer
            </button>
            <a href="{{ route('auth-tokens.index') }}" class="inline-flex items-center px-4 py-2 border px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="mt-6 bg-white shadow overflow-x-scroll sm:rounded-lg
    [&::-webkit-scrollbar]:h-1
    [&::-webkit-scrollbar-track]:rounded-full
    [&::-webkit-scrollbar-track]:bg-gray-100
    [&::-webkit-scrollbar-thumb]:rounded-full
    [&::-webkit-scrollbar-thumb]:bg-gray-300
    dark:[&::-webkit-scrollbar-track]:bg-gray-700
    dark:[&::-webkit-scrollbar-thumb]:bg-gray-500">
    <table class="min-w-full divide-y divide-gray-200 overflow-x-scroll">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Authentification</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merchant</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expire le</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé le</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tokens as $token)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $token->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($token->auth)
                                <a href="{{ route('auths.show', $token->auth) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $token->auth->code }}
                                </a>
                            @else
                                Aucune authentification
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $token->auth && $token->auth->merchant ? $token->auth->merchant->name : 'Aucun' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($token->active && (!$token->expires_at || $token->expires_at->isFuture()))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                        @elseif($token->expires_at && $token->expires_at->isPast())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Expiré
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $token->expires_at ? $token->expires_at->format('d/m/Y H:i') : 'Jamais' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $token->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('auth-tokens.show', $token) }}" class="text-blue-600 hover:text-blue-900">
                                <i class='bx bx-show text-xl'  ></i>
                            </a>
                            <a href="{{ route('auth-tokens.edit', $token) }}" class="text-indigo-600 hover:text-indigo-900">
                                <i class="bx bx-edit text-xl"></i>
                            </a>
                            <form method="POST" action="{{ route('auth-tokens.destroy', $token) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce token ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="bx bx-trash text-xl"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        Aucun token trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $tokens->links() }}
</div>
@endsection
