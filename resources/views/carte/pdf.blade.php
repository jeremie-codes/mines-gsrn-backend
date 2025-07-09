<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>

        @page {
            margin: 0;
            size: 1012px 638px; /* Taille de la carte */
            font-family: DejaVu Sans;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .carte {
            width: 1012px;
            height: 638px;
            position: relative;
            background-image: url('{{ public_path("images/cartefront.jpg") }}');
            background-size: cover;
        }
        .field {
            position: absolute;
            font-size: 25px;
            color: #000;
            font-weight: bold;
        }
        .photo-bloc {
            position: absolute;
        }

        .photo {
            position: relative;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="carte">

    {{-- Bloc pour la photo --}}
    <div class="photo-bloc" style="top: 163px; left: 82px; width: 288px; height: 347px; overflow: hidden; border: 1px solid #ccc;">
        {{-- Affichage de la photo --}}
        <img
         class="photo"
            src="{{ public_path('storage/' . $member->face_path) }}"
            style="top: {{ $positions['photo']['top'] }}px; max-width: 600px; left: {{ $positions['photo']['left'] }}px; width: {{ $photoWidth }}px; height: {{ $photoHeight }}px;"
        />
    </div>

    {{-- Champs de la carte --}}

    <div class="field" style="top: {{ $positions['nom']['top'] }}px; left: {{ $positions['nom']['left'] }}px;">{{ $member->firstname }}</div>
    <div class="field" style="top: {{ $positions['postnom']['top'] }}px; left: {{ $positions['postnom']['left'] }}px;">{{ $member->middlename }}</div>
    <div class="field" style="top: {{ $positions['prenom']['top'] }}px; left: {{ $positions['prenom']['left'] }}px;">{{ $member->lastname }}</div>
    <div class="field" style="top: {{ $positions['fonction']['top'] }}px; left: {{ $positions['fonction']['left'] }}px;">{{ $member->fonction->name ?? '' }}</div>
    <div class="field" style="top: {{ $positions['categorie']['top'] }}px; left: {{ $positions['categorie']['left'] }}px;">{{ $member->categorie }}</div>
    <div class="field" style="top: {{ $positions['site']['top'] }}px; left: {{ $positions['site']['left'] }}px;">{{ $member->site->name ?? '' }}</div>
    <div class="field" style="top: {{ $positions['numero']['top'] }}px; left: {{ $positions['numero']['left'] }}px;">{{ $member->membershipNumber }}</div>

    <img
        src="qrcodeaaaaa                                                                                                                                                                                                                                                                                                                            .php?s=qrl&d=000000000000000000000000000000000000000000000000"
        style="position: absolute; bottom: 20px; right: 50px; width: 165px; height: 165px;"
        alt="QR Code"
    />

</div>

<div style="page-break-after: always;"></div>

{{-- Deuxième page : verso --}}
<div class="carte" style="background-image: url('{{ public_path("images/carteback.jpg") }}')"></div>

</body>
</html>
