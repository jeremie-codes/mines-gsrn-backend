@extends('layouts.app')

@section('title', 'Liste des Pools')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Liste des Pools</h1>
    <a href="{{ route('pools.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouveau Pool
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
                        <th>Site</th>
                        <th>Description</th>
                        <th>Membres</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pools as $pool)
                        <tr>
                            <td>{{ $pool->id }}</td>
                            <td>{{ $pool->name }}</td>
                            <td>{{ $pool->site->name }}</td>
                            <td>{{ Str::limit($pool->description, 50) ?? 'Aucune' }}</td>
                            <td>{{ $pool->members->count() }}</td>
                            <td>
                                <span class="badge bg-{{ $pool->is_active ? 'success' : 'danger' }}">
                                    {{ $pool->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('pools.show', $pool) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pools.edit', $pool) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pools.destroy', $pool) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce pool ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucun pool trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $pools->links() }}
    </div>
</div>
@endsection