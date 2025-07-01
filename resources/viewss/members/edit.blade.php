@extends('layouts.app')

@section('title', 'Modifier le Membre')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Modifier le Membre: {{ $member->full_name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="firstname" class="form-label">Prénom</label>
                                <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                                       id="firstname" name="firstname" value="{{ old('firstname', $member->firstname) }}">
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="middlename" class="form-label">Nom du milieu</label>
                                <input type="text" class="form-control @error('middlename') is-invalid @enderror"
                                       id="middlename" name="middlename" value="{{ old('middlename', $member->middlename) }}">
                                @error('middlename')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="lastname" class="form-label">Nom de famille</label>
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                                       id="lastname" name="lastname" value="{{ old('lastname', $member->lastname) }}">
                                @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="membershipNumber" class="form-label">Numéro d'adhésion</label>
                                <input type="text" class="form-control @error('membershipNumber') is-invalid @enderror"
                                       id="membershipNumber" name="membershipNumber" value="{{ old('membershipNumber', $member->membershipNumber) }}">
                                @error('membershipNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $member->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="site_id" class="form-label">Site *</label>
                        <select class="form-select @error('site_id') is-invalid @enderror"
                                id="site_id" name="site_id" required>
                            <option value="">Sélectionner un site</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}"
                                        {{ old('site_id', $member->site_id) == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('site_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pool_id" class="form-label">Pool</label>
                                <select class="form-select @error('pool_id') is-invalid @enderror"
                                        id="pool_id" name="pool_id">
                                    <option value="">Sélectionner un pool</option>
                                    @foreach($pools as $pool)
                                        <option value="{{ $pool->id }}"
                                                {{ old('pool_id', $member->pool_id) == $pool->id ? 'selected' : '' }}>
                                            {{ $pool->name }} ({{ $pool->site->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('pool_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="libelle_pool" class="form-label">Libellé Pool (si autre)</label>
                                <input type="text" class="form-control @error('libelle_pool') is-invalid @enderror"
                                       id="libelle_pool" name="libelle_pool" value="{{ old('libelle_pool', $member->libelle_pool) }}">
                                @error('libelle_pool')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fonction_id" class="form-label">Fonction</label>
                        <select class="form-select @error('fonction_id') is-invalid @enderror"
                                id="fonction_id" name="fonction_id">
                            <option value="">Sélectionner une fonction</option>
                            @foreach($fonctions as $fonction)
                                <option value="{{ $fonction->id }}"
                                        {{ old('fonction_id', $member->fonction_id) == $fonction->id ? 'selected' : '' }}>
                                    {{ $fonction->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('fonction_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="face_image" class="form-label">Photo de profil</label>

                        @if($member->face_path)
                            <div class="mb-2">
                                <img src="{{ Storage::url($member->face_path) }}"
                                     alt="Photo actuelle"
                                     class="img-thumbnail"
                                     style="max-width: 150px; max-height: 150px;">
                                <p class="text-muted small">Photo actuelle</p>
                            </div>
                        @endif

                        <input type="file" class="form-control @error('face_image') is-invalid @enderror"
                               id="face_image" name="face_image" accept="image/*">
                        <small class="text-muted">Formats acceptés: JPG, PNG, GIF (max 2MB). Laissez vide pour conserver la photo actuelle.</small>
                        @error('face_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active"
                                   name="is_active" value="1" {{ old('is_active', $member->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Membre actif
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('members.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Prévisualisation de l'image
document.getElementById('face_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Créer ou mettre à jour l'aperçu de l'image
            let preview = document.getElementById('image-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'image-preview';
                preview.className = 'mt-2 img-thumbnail';
                preview.style.maxWidth = '150px';
                preview.style.maxHeight = '150px';
                document.getElementById('face_image').parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
