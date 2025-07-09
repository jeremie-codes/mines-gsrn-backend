<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Prévisualisation Carte Dynamique</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <style>

    * {
        font-family: DejaVu Sans;
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
      font-size: 26px;
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
<body class="bg-gray-100 p-6">

<div class="flex flex-col lg:flex-row gap-6 items-center justify-center">

  {{-- Carte dynamique --}}
  <div class="carte relative shadow-lg border" id="carte">
    
    <div class="photo-bloc border border-gray-600" style="top: 163px; left: 82px; width: 288px; height: 347px; overflow: hidden;">
        <img
        class="photo"
        id="photo"
        src="{{ asset('storage/' . $member->face_path) }}"
        style="top: 0; left: 0; max-width: 600px; width: 288px; height: 347px;"
        />
    </div>

    <div class="field" id="nom" style="top: 209px; left: 709px">{{ $member->firstname }}</div>
    <div class="field" id="postnom" style="top: 250px; left: 709px">{{ $member->middlename }}</div>
    <div class="field" id="prenom" style="top: 290px; left: 709px">{{ $member->lastname }}</div>
    <div class="field" id="fonction" style="top: 331px; left: 709px">{{ $member->fonction->name ?? '' }}</div>
    <div class="field" id="categorie" style="top: 375px; left: 709px">{{ $member->categorie }}kdkdk</div>
    <div class="field" id="site" style="top: 416px; left: 709px">{{ $member->site->name ?? '' }}</div>
    <div class="field" id="numero" style="top: 560px; left: 95px">{{ $member->membershipNumber }}</div>

    <div style="position: absolute; bottom: 20px; right: 50px; width: 165px; height: 165px;">
        {!! $qrCodeSvg !!}
    </div>
  </div>

  {{-- Contrôles sliders --}}
  <form method="POST" action="{{ route('carte.pdf.generate') }}" id="pdfForm" class="w-ful max-w-xl bg-white p-6 rounded shadow">
    @csrf
    <input type="hidden" name="member_id" value="{{ $member->id }}">

    <h2 class="text-md font-bold mb-4 flex items-center gap-2 text-gray-800">
    <i class='bx bx-cog text-2xl text-green-600'></i>
    Configuration de la carte
    </h2>

    @foreach([
        'nom' => [209, 709],
        'postnom' => [250, 709],
        'prenom' => [290, 709],
        'fonction' => [331, 709],
        'categorie' => [375, 709],
        'site' => [416, 709],
        'numero' => [560, 95]
    ] as $field => $defaultPosition)
      <div class="mb-1">
        <p class="font-semibold text-gray-700 capitalize">{{ $field }}</p>
        <div class="flex gap-2 items-center">
          <label class="text-sm w-16">Top</label>
          <input type="range" name="{{ $field }}_top" min="0" max="638" value="{{ $defaultPosition[0] }}" oninput="move('{{ $field }}', this.value, null)" class="w-full">
        </div>
        <div class="flex gap-2 items-center mt-">
          <label class="text-sm w-16">Left</label>
          <input type="range" name="{{ $field }}_left" min="0" max="1012" value="{{ $defaultPosition[1] }}" oninput="move('{{ $field }}', null, this.value)" class="w-full">
        </div>
      </div>
    @endforeach

    {{-- Photo: top, left, width, height --}}
    <div class="mb-6">
      <p class="font-semibold text-gray-700">photo</p>
      <div class="flex gap-2 items-center">
        <label class="text-sm w-16">Top</label>
        <input type="range" name="photo_top" min="-347" max="347" value="0" oninput="move('photo', this.value, null)" class="w-full">
      </div>
      <div class="flex gap-2 items-center mt-1">
        <label class="text-sm w-16">Left</label>
        <input type="range" name="photo_left" min="-288" max="288" value="0" oninput="move('photo', null, this.value)" class="w-full">
      </div>
      <div class="flex gap-2 items-center mt-1">
        <label class="text-sm w-16">Largeur</label>
        <input type="range" name="photo_width" min="0" max="600" value="288" oninput="resize('photo', this.value, null)" class="w-full">
      </div>
      <div class="flex gap-2 items-center mt-1">
        <label class="text-sm w-16">Hauteur</label>
        <input type="range" name="photo_height" min="0" max="600" value="347" oninput="resize('photo', null, this.value)" class="w-full">
      </div>
    </div>

    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded text-lg">📄 Générer le PDF</button>
  </form>
  
</div>

<script>
  function move(field, top, left) {
    const el = document.getElementById(field);
    if (top !== null) el.style.top = top + 'px';
    if (left !== null) el.style.left = left + 'px';

    console.log(top, left);
    
  }

  function resize(field, width, height) {
    const el = document.getElementById(field);

    if (width !== null) el.style.width = width + 'px';
    if (height !== null) el.style.height = height + 'px';
  }
</script>

</body>
</html>
