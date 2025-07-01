@extends('layouts.app')

@section('title', 'Détails du Pool')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Détails du Pool: {{ $pool->name }}</h4>
                <div>
                    <a href="{{ route('pools.edit', $pool) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('pools.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations Générales</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $pool->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nom:</strong></td>
                                <td>{{ $pool->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Site:</strong></td>
                                <td>
                                    <a href="{{ route('sites.show', $pool->site) }}" class="text-decoration-none">
                                        {{ $pool->site->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Statut:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $pool->is_active ? 'success' : 'danger' }}">
                                        {{ $pool->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Créé le:</strong></td>
                                <td>{{ $pool->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Modifié le:</strong></td>
                                <td>{{ $pool->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Description</h6>
                        <p class="text-muted">
                            {{ $pool->description ?? 'Aucune description disponible' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Statistiques</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h3 class="text-primary">{{ $pool->members->count() }}</h3>
                    <p class="text-muted">Membres dans ce pool</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if($pool->members->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Membres du Pool</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom Complet</th>
                        <th>Téléphone</th>
                        <th>Fonction</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pool->members as $member)
                        <tr>
                            <td>{{ $member->full_name }}</td>
                            <td>{{ $member->phone ?? 'Non spécifié' }}</td>
                            <td>{{ $member->fonction->nom ?? 'Aucune' }}</td>
                            <td>
                                <span class="badge bg-{{ $member->is_active ? 'success' : 'danger' }}">
                                    {{ $member->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('members.show', $member) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection