@extends('layouts.app')

@section('title', 'Liste des Sites')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Liste des Sites</h1>
    <a href="{{ route('sites.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouveau Site
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Localisation</th>
                        <th>Pools</th>
                        <th>Membres</th>
                        <th>Compteur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sites as $site)
                        <tr>
                            <td>{{ $site->id }}</td>
                            <td>{{ $site->name }}</td>
                            <td><span class="badge bg-secondary">{{ $site->code }}</span></td>
                            <td>{{ $site->location ?? 'Non spécifiée' }}</td>
                            <td>{{ $site->pools->count() }}</td>
                            <td>{{ $site->members->count() }}</td>
                            <td>
                                <span class="badge bg-info">{{ $site->membership_counter }}</span>
                                <br><small class="text-muted">membres créés</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $site->is_active ? 'success' : 'danger' }}">
                                    {{ $site->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('sites.show', $site) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sites.edit', $site) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('sites.destroy', $site) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce site ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Aucun site trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $sites->links() }}
    </div>
</div>
@endsection