<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>

        @page {
            margin: 0;
            size: 1012px 638px; /* Taille de la carte */
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
            font-size: 24px;
            color: #000;
            font-weight: bold;
        }
        .photo {
            position: absolute;
            object-fit: cover;
        }

    </style>
</head>
<body>

<div class="carte">
    <img
        class="photo"
        src="{{ public_path('storage/' . $member->face_path) }}"
        style="top: {{ $positions['photo']['top'] }}px; left: {{ $positions['photo']['left'] }}px; width: {{ $photoWidth }}px; height: {{ $photoHeight }}px;"
    />

    <div class="field" style="top: {{ $positions['nom']['top'] }}px; left: {{ $positions['nom']['left'] }}px;">{{ $member->firstname }}</div>
    <div class="field" style="top: {{ $positions['postnom']['top'] }}px; left: {{ $positions['postnom']['left'] }}px;">{{ $member->middlename }}</div>
    <div class="field" style="top: {{ $positions['prenom']['top'] }}px; left: {{ $positions['prenom']['left'] }}px;">{{ $member->lastname }}</div>
    <div class="field" style="top: {{ $positions['fonction']['top'] }}px; left: {{ $positions['fonction']['left'] }}px;">{{ $member->fonction->name ?? '' }}</div>
    <div class="field" style="top: {{ $positions['categorie']['top'] }}px; left: {{ $positions['categorie']['left'] }}px;">{{ $member->categorie }}</div>
    <div class="field" style="top: {{ $positions['site']['top'] }}px; left: {{ $positions['site']['left'] }}px;">{{ $member->site->name ?? '' }}</div>
    <div class="field" style="top: {{ $positions['numero']['top'] }}px; left: {{ $positions['numero']['left'] }}px;">{{ $member->membershipNumber }}</div>
</div>

<div style="page-break-after: always;"></div>

{{-- Deuxième page : verso --}}
<div class="carte" style="background-image: url('{{ public_path("images/carteback.jpg") }}')"></div>

</body>
</html>
