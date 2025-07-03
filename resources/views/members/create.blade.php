@extends('layouts.app')

@section('title', 'Créer un Membre')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Créer un Nouveau Membre</h4>
                <small class="text-muted">Le numéro d'adhésion sera généré automatiquement</small>
            </div>
            <div class="card-body">
                <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="firstname" class="form-label">Prénom</label>
                                <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                                       id="firstname" name="firstname" value="{{ old('firstname') }}">
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="middlename" class="form-label">Nom du milieu</label>
                                <input type="text" class="form-control @error('middlename') is-invalid @enderror"
                                       id="middlename" name="middlename" value="{{ old('middlename') }}">
                                @error('middlename')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="lastname" class="form-label">Nom de famille</label>
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                                       id="lastname" name="lastname" value="{{ old('lastname') }}">
                                @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="city_id" class="form-label">Ville</label>
                                <select class="form-select @error('city_id') is-invalid @enderror"
                                        id="city_id" name="city_id">
                                    <option value="">Sélectionner une ville</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }} ({{ $city->country->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="township_id" class="form-label">Commune</label>
                                <select class="form-select @error('township_id') is-invalid @enderror"
                                        id="township_id" name="township_id">
                                    <option value="">Sélectionner une commune</option>
                                    @foreach($townships as $township)
                                        <option value="{{ $township->id }}"
                                                data-city-id="{{ $township->city_id }}"
                                                {{ old('township_id') == $township->id ? 'selected' : '' }}
                                                style="display: none;">
                                            {{ $township->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('township_id')
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
                                <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }} ({{ $site->code }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Le code du site sera utilisé pour générer le numéro d'adhésion</small>
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
                                        <option value="{{ $pool->id }}" {{ old('pool_id') == $pool->id ? 'selected' : '' }}>
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
                                       id="libelle_pool" name="libelle_pool" value="{{ old('libelle_pool') }}">
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
                                <option value="{{ $fonction->id }}" {{ old('fonction_id') == $fonction->id ? 'selected' : '' }}>
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
                        <input type="file" class="form-control @error('face_image') is-invalid @enderror"
                               id="face_image" accept="image/*">
                        <small class="text-muted">Formats acceptés: JPG, PNG, GIF (max 2MB)</small>
                        @error('face_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <input type="hidden" name="face_base64" id="face_base64">
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active"
                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Membre actif
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Génération automatique du numéro d'adhésion:</strong><br>
                        Format: <code>PROVINCE + SITE + 00001 + ANNÉE</code><br>
                        Exemple: <code>KNDPN0000125</code> (KND = Province, PN = Site, 00001 = Compteur, 25 = Année)
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('members.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer le Membre
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

    if (!file) return;

    const reader = new FileReader();
    reader.onloadend = function() {
        const base64String = reader.result;
        document.getElementById('face_base64').value = base64String;
    };
    reader.readAsDataURL(file);

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

// Filtrer les communes selon la ville sélectionnée
document.getElementById('city_id').addEventListener('change', function() {
    const cityId = this.value;
    const townshipSelect = document.getElementById('township_id');
    const townshipOptions = townshipSelect.querySelectorAll('option');

    // Réinitialiser la sélection
    townshipSelect.value = '';

    // Afficher/masquer les options selon la ville
    townshipOptions.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else if (option.dataset.cityId === cityId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
});

// Initialiser le filtre au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const citySelect = document.getElementById('city_id');
    if (citySelect.value) {
        citySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
