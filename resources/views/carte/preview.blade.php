<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prévisualisation Carte Dynamique</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
        }

        .container {
            display: flex;
            flex-direction: row;
        }

        .carte {
            width: 1012px;
            height: 638px;
            position: relative;
            background-image: url('{{ asset("images/cartefront.jpg") }}');
            background-size: cover;
            background-repeat: no-repeat;
            margin: 20px;
            border: 1px solid #ccc;
        }

        .field {
            position: absolute;
            font-size: 20px;
            color: #000;
            font-weight: bold;
        }

        .photo {
            position: absolute;
            object-fit: cover;
            width: 310px;
            height: 430px;
        }

        .controls {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .controls label {
            font-weight: bold;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        button {
            padding: 10px 15px;
            background: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background: darkgreen;
        }
    </style>
</head>
<body>

<div class="container">
    {{-- Carte dynamique --}}
    <div class="carte" id="carte">
        <img class="photo" id="photo" src="{{ asset('storage/' . $member->face_path) }}" />

        <div class="field" style="top: 227px; left: 580px" id="nom">{{ $member->firstname }}</div>
        <div class="field" style="top: 227px; left: 580px" id="postnom">{{ $member->middlename }}</div>
        <div class="field" style="top: 227px; left: 580px" id="prenom">{{ $member->lastname }}</div>
        <div class="field" style="top: 227px; left: 580px" id="fonction">{{ $member->fonction->name ?? '' }}</div>
        <div class="field" style="top: 227px; left: 580px" id="categorie">{{ $member->categorie }}</div>
        <div class="field" style="top: 227px; left: 580px" id="site">{{ $member->site->name ?? '' }}</div>
        <div class="field" style="top: 227px; left: 580px" id="numero">{{ $member->membershipNumber }}</div>
    </div>

    {{-- Contrôles sliders --}}
    <form method="POST" action="{{ route('carte.pdf.generate') }}" id="pdfForm">
        @csrf
        <input type="hidden" name="member_id" value="{{ $member->id }}">
        <div class="controls">
            @foreach(['nom' => 227, 'postnom' => 227, 'prenom' => 227, 'fonction' => 227, 'categorie' => 227, 'site' => 27, 'numero' => 227, 'photo' => 227] as $field => $defaultTop)
                <div class="control-group">
                    <label>{{ ucfirst($field) }} - Haut (Hauteur)</label>
                    <input type="range" name="{{ $field }}_top" min="0" max="638" value="{{ $defaultTop }}" oninput="move('{{ $field }}', this.value, null)">
                    <label>{{ ucfirst($field) }} - Gauche (Largeur)</label>
                    <input type="range" name="{{ $field }}_left" min="0" max="1012" value="580" oninput="move('{{ $field }}', null, this.value)">
                </div>
            @endforeach

            <button type="submit">Générer le PDF</button>
        </div>
    </form>
</div>

<script>
    const positions = {};

    function move(field, top, left) {
        const el = document.getElementById(field);
        if (top !== null) {
            el.style.top = top + 'px';
            positions[field + '_top'] = top;
        }
        if (left !== null) {
            console.log('Moving field:', field, 'Top:', top, 'Left:', left);
            el.style.left = left + 'px';
            positions[field + '_left'] = left;
        }
    }
</script>

</body>
</html>
