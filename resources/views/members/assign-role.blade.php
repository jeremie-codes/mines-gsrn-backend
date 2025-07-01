@extends('layouts.app')

@section('title', 'Assigner un Rôle')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Assigner un Rôle à: {{ $member->full_name }}</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Informations du Membre:</h6>
                    <p class="text-muted">
                        <strong>Nom:</strong> {{ $member->full_name }}<br>
                        <strong>Site:</strong> {{ $member->site->name }}<br>
                        <strong>Pool:</strong> {{ $member->pool->name ?? 'Aucun' }}<br>
                        @if($member->user)
                            <strong>Rôle actuel:</strong> {{ $member->user->role->name }}
                        @endif
                    </p>
                </div>
                
                <hr>
                
                @if($member->user)
                    <form action="{{ route('members.assign-role.store', $member) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Nouveau Rôle *</label>
                            <select class="form-select @error('role_id') is-invalid @enderror" 
                                    id="role_id" name="role_id" required>
                                <option value="">Sélectionner un rôle</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" 
                                            {{ $member->user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('members.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-tag"></i> Assigner le Rôle
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        Ce membre n'a pas encore de compte utilisateur. 
                        <a href="{{ route('members.create-user', $member) }}" class="btn btn-sm btn-success ms-2">
                            Créer un utilisateur
                        </a>
                    </div>
                    
                    <div class="d-flex justify-content-start">
                        <a href="{{ route('members.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection