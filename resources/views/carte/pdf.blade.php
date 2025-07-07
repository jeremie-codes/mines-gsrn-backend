<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
        }

        .carte {
            width: 1012px;
            height: 638px;
            position: relative;
            background-image: url('{{ public_path('storage/cartes/carte_recto.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
        }

        .field {
            position: absolute;
            font-size: 20px;
            color: #000;
            font-family: sans-serif;
            font-weight: bold;
        }

        .photo {
            position: absolute;
            top: 100px;
            left: 85px;
            width: 310px;
            height: 430px;
            object-fit: cover;
        }

        .nom { top: 180px; left: 470px; }
        .postnom { top: 210px; left: 470px; }
        .prenom { top: 240px; left: 470px; }
        .fonction { top: 270px; left: 470px; }
        .categorie { top: 300px; left: 470px; }
        .site { top: 330px; left: 470px; }
        .numero { bottom: 40px; left: 90px; font-size: 24px; }
    </style>
</head>
<body>

    <div class="carte">
        <img class="photo" src="{{ public_path('storage/photos/' . $member->photo) }}" alt="Photo">
        <div class="field nom" style="position: absolute; top: {{ $positions['nom_top'] ?? 0 }}px; left: {{ $positions['nom_left'] ?? 0 }}px;">{{ $member->nom }}</div>
        <div class="field postnom">{{ $member->postnom }}</div>
        <div class="field prenom">{{ $member->prenom }}</div>
        <div class="field fonction">{{ $member->fonction }}</div>
        <div class="field categorie">{{ $member->categorie }}</div>
        <div class="field site">{{ $member->site_exploitation }}</div>
        <div class="field numero">{{ $member->numero }}</div>
    </div>

</body>
</html>
