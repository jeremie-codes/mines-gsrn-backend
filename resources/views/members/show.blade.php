@extends('layouts.app')

@section('title', 'Détails du Membre')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Détails du Membre: {{ $member->full_name }}</h4>
                <div>
                    <a href="{{ route('members.edit', $member) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        @if($member->face_path)
                            <img src="{{ Storage::url($member->face_path) }}" 
                                 alt="Photo de {{ $member->full_name }}" 
                                 class="img-fluid rounded-circle mb-3" 
                                 style="max-width: 150px; max-height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" 
                                 style="width: 150px; height: 150px; margin: 0 auto;">
                                <i class="fas fa-user fa-3x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informations Personnelles</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>{{ $member->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Prénom:</strong></td>
                                        <td>{{ $member->firstname ?? 'Non spécifié' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nom de famille:</strong></td>
                                        <td>{{ $member->lastname ?? 'Non spécifié' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nom du milieu:</strong></td>
                                        <td>{{ $member->middlename ?? 'Non spécifié' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Numéro d'adhésion:</strong></td>
                                        <td>{{ $member->membershipNumber ?? 'Non spécifié' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Téléphone:</strong></td>
                                        <td>{{ $member->phone ?? 'Non spécifié' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Statut:</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $member->is_active ? 'success' : 'danger' }}">
                                                {{ $member->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Localisation & Affectations</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Ville:</strong></td>
                                        <td>{{ $member->city->name ?? 'Non spécifiée' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Commune:</strong></td>
                                        <td>{{ $member->township->name ?? 'Non spécifiée' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Site:</strong></td>
                                        <td>
                                            <a href="{{ route('sites.show', $member->site) }}" class="text-decoration-none">
                                                {{ $member->site->name }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pool:</strong></td>
                                        <td>
                                            @if($member->pool)
                                                <a href="{{ route('pools.show', $member->pool) }}" class="text-decoration-none">
                                                    {{ $member->pool->name }}
                                                </a>
                                            @else
                                                {{ $member->libelle_pool ?? 'Aucun' }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fonction:</strong></td>
                                        <td>{{ $member->fonction->nom ?? 'Aucune' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Adresse complète:</strong></td>
                                        <td>{{ $member->full_address }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Compte utilisateur:</strong></td>
                                        <td>
                                            @if($member->user)
                                                <span class="badge bg-success">Oui</span>
                                                <br>
                                                <small class="text-muted">
                                                    <a href="{{ route('users.show', $member->user) }}" class="text-decoration-none">
                                                        {{ $member->user->username }}
                                                    </a>
                                                </small>
                                            @else
                                                <span class="badge bg-secondary">Non</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Dates</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Créé le:</strong></td>
                                        <td>{{ $member->created_at->format('d/m/Y à H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Modifié le:</strong></td>
                                        <td>{{ $member->updated_at->format('d/m/Y à H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(!$member->hasUser())
                        <a href="{{ route('members.create-user', $member) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-user-plus"></i> Créer un Utilisateur
                        </a>
                    @endif
                    
                    @if($member->user)
                        <a href="{{ route('members.assign-role', $member) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-tag"></i> Assigner un Rôle
                        </a>
                        <a href="{{ route('users.show', $member->user) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-user"></i> Voir l'Utilisateur
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        @if($member->user)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Informations Utilisateur</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $member->user->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Rôle:</strong></td>
                        <td>
                            <span class="badge bg-info">{{ $member->user->role->name }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Statut:</strong></td>
                        <td>
                            <span class="badge bg-{{ $member->user->is_active ? 'success' : 'danger' }}">
                                {{ $member->user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection