@extends('layouts.app')

@section('title', 'Détails de l\'Utilisateur')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Détails de l'Utilisateur: {{ $user->username }}</h4>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations de Connexion</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nom d'utilisateur:</strong></td>
                                <td>{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rôle:</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $user->role->name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Statut:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Créé le:</strong></td>
                                <td>{{ $user->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Modifié le:</strong></td>
                                <td>{{ $user->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informations du Membre</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nom complet:</strong></td>
                                <td>
                                    <a href="{{ route('members.show', $user->member) }}" class="text-decoration-none">
                                        {{ $user->member->full_name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Téléphone:</strong></td>
                                <td>{{ $user->member->phone ?? 'Non spécifié' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Site:</strong></td>
                                <td>
                                    <a href="{{ route('sites.show', $user->member->site) }}" class="text-decoration-none">
                                        {{ $user->member->site->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Pool:</strong></td>
                                <td>
                                    @if($user->member->pool)
                                        <a href="{{ route('pools.show', $user->member->pool) }}" class="text-decoration-none">
                                            {{ $user->member->pool->name }}
                                        </a>
                                    @else
                                        Aucun
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Fonction:</strong></td>
                                <td>{{ $user->member->fonction->nom ?? 'Aucune' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Permissions du Rôle</h6>
            </div>
            <div class="card-body">
                @if($user->role->permissions->count() > 0)
                    <div class="row">
                        @foreach($user->role->permissions as $permission)
                            <div class="col-12 mb-2">
                                <span class="badge bg-secondary">{{ $permission->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Aucune permission assignée</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection