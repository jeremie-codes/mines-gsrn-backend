@extends('layouts.app')

@section('title', 'Authentifications - SMS Gateway')
@section('header', 'Gestion des Authentifications')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700">Liste de toutes les authentifications avec leurs informations et statuts.</p>
    </div>
    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <a href="{{ route('auths.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
            Nouvelle Authentification
        </a>
    </div>
</div>

<!-- Filters -->
<div class="mt-6 bg-white shadow rounded-lg p-4">
    <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nom d'utilisateur ou code..." class="mt-1 block w-full rounded-md px-4 py-2 border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
        <div>
            <label for="active" class="block text-sm font-medium text-gray-700">Statut</label>
            <select name="active" id="active" class="mt-1 block w-full rounded-md px-4 py-2 border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Tous</option>
                <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Actif</option>
                <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>
        <div>
            <label for="merchant_id" class="block text-sm font-medium text-gray-700">Merchant</label>
            <select name="merchant_id" id="merchant_id" class="mt-1 block w-full rounded-md px-4 py-2 border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Tous les merchants</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->id }}" {{ request('merchant_id') == $merchant->id ? 'selected' : '' }}>
                        {{ $merchant->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Filtrer
            </button>
            <a href="{{ route('auths.index') }}" class="inline-flex items-center px-4 py-2 border px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
    <table class="min-w-full divide-y divide-gray-200  overflow-x-scroll">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merchant</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tokens</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Messages</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé le</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($auths as $auth)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $auth->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $auth->username ?? 'Non défini' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $auth->merchant ? $auth->merchant->name : 'Aucun' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($auth->active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $auth->authTokens()->count() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $auth->messages()->count() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $auth->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('auths.show', $auth) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="bx bx-show text-xl"></i>
                            </a>
                            <a href="{{ route('auths.edit', $auth) }}" class="text-yellow-600 hover:text-yellow-900">
                                <i class="bx bx-edit text-xl"></i>
                            </a>
                            <form method="POST" action="{{ route('auths.destroy', $auth) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette authentification ?')">
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
                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        Aucune authentification trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $auths->links() }}
</div>
@endsection
