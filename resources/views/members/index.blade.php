@extends('layouts.app')

@section('title', 'Liste des Membres')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Liste des Membres</h1>
    <a href="{{ route('members.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouveau Membre
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom Complet</th>
                        <th>Téléphone</th>
                        <th>Ville/Commune</th>
                        <th>Site</th>
                        <th>Pool</th>
                        <th>Fonction</th>
                        <th>Utilisateur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>{{ $member->id }}</td>
                            <td>{{ $member->full_name }}</td>
                            <td>{{ $member->phone ?? 'Non spécifié' }}</td>
                            <td>
                                @if($member->city || $member->township)
                                    {{ $member->city->name ?? '' }}
                                    @if($member->township)
                                        <br><small class="text-muted">{{ $member->township->name }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </td>
                            <td>{{ $member->site->name }}</td>
                            <td>{{ $member->pool->name ?? 'Aucun' }}</td>
                            <td>{{ $member->fonction->nom ?? 'Aucune' }}</td>
                            <td>
                                @if($member->user)
                                    <span class="badge bg-success">Oui</span>
                                @else
                                    <span class="badge bg-secondary">Non</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $member->is_active ? 'success' : 'danger' }}">
                                    {{ $member->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('members.show', $member) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if(!$member->hasUser())
                                        <a href="{{ route('members.create-user', $member) }}" class="btn btn-outline-success" title="Créer un utilisateur">
                                            <i class="fas fa-user-plus"></i>
                                        </a>
                                    @endif
                                    
                                    @if($member->user)
                                        <a href="{{ route('members.assign-role', $member) }}" class="btn btn-outline-purple" title="Assigner un rôle">
                                            <i class="fas fa-user-tag"></i>
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('members.destroy', $member) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce membre ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Aucun membre trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $members->links() }}
    </div>
</div>
@endsection

@section('scripts')
<style>
.btn-outline-purple {
    color: #6f42c1;
    border-color: #6f42c1;
}
.btn-outline-purple:hover {
    color: #fff;
    background-color: #6f42c1;
    border-color: #6f42c1;
}
</style>
@endsection