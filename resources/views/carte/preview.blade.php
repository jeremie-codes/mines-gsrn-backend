<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Prévisualisation Carte Dynamique</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
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
    }
  </style>
</head>
<body class="bg-gray-100 p-6">

<div class="flex flex-wrap gap-6 justify-center">

  {{-- Carte dynamique --}}
  <div class="carte relative shadow-lg border" id="carte">
    <img
      class="photo"
      id="photo"
      src="{{ asset('storage/' . $member->face_path) }}"
      style="top: 165px; left: 84px; width: 310px; height: 430px;"
    />

    <div class="field" id="nom" style="top: 227px; left: 709px">{{ $member->firstname }}</div>
    <div class="field" id="postnom" style="top: 265px; left: 709px">{{ $member->middlename }}</div>
    <div class="field" id="prenom" style="top: 305px; left: 709px">{{ $member->lastname }}</div>
    <div class="field" id="fonction" style="top: 346px; left: 709px">{{ $member->fonction->name ?? '' }}</div>
    <div class="field" id="categorie" style="top: 383px; left: 709px">{{ $member->categorie }}</div>
    <div class="field" id="site" style="top: 428px; left: 709px">{{ $member->site->name ?? '' }}</div>
    <div class="field" id="numero" style="top: 538px; left: 435px">{{ $member->membershipNumber }}</div>
  </div>

  {{-- Contrôles sliders --}}
  <form method="POST" action="{{ route('carte.pdf.generate') }}" id="pdfForm" class="w-full max-w-xl bg-white p-6 rounded shadow">
    @csrf
    <input type="hidden" name="member_id" value="{{ $member->id }}">

    <h2 class="text-xl font-bold mb-4">🔧 Configuration de la carte</h2>

    @foreach(['nom' => 227, 'postnom' => 265, 'prenom' => 305, 'fonction' => 346, 'categorie' => 383, 'site' => 428, 'numero' => 538, 'photo' => 165] as $field => $defaultTop)
      <div class="mb-6">
        <p class="font-semibold text-gray-700 capitalize">{{ $field }}</p>
        <div class="flex gap-2 items-center">
          <label class="text-sm">Top</label>
          <input type="range" name="{{ $field }}_top" min="0" max="638" value="{{ $defaultTop }}" oninput="move('{{ $field }}', this.value, null)" class="w-full">
        </div>
        <div class="flex gap-2 items-center mt-1">
          <label class="text-sm">Left</label>
          <input type="range" name="{{ $field }}_left" min="0" max="1012" value="709" oninput="move('{{ $field }}', null, this.value)" class="w-full">
        </div>

        @if($field === 'photo')
          <div class="flex items-center gap-2 mt-2">
            <button type="button" onclick="resizePhoto('increaseWidth')" class="bg-blue-500 text-white px-3 py-1 rounded">↔️ +L</button>
            <button type="button" onclick="resizePhoto('decreaseWidth')" class="bg-blue-500 text-white px-3 py-1 rounded">↔️ -L</button>
            <button type="button" onclick="resizePhoto('increaseHeight')" class="bg-green-500 text-white px-3 py-1 rounded">↕️ +H</button>
            <button type="button" onclick="resizePhoto('decreaseHeight')" class="bg-green-500 text-white px-3 py-1 rounded">↕️ -H</button>
          </div>
        @endif
      </div>
    @endforeach

    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded text-lg">📄 Générer le PDF</button>
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
      el.style.left = left + 'px';
      positions[field + '_left'] = left;
    }
  }

  function resizePhoto(action) {
    const photo = document.getElementById('photo');
    let width = parseInt(photo.style.width);
    let height = parseInt(photo.style.height);

    switch(action) {
      case 'increaseWidth':
        width += 10;
        break;
      case 'decreaseWidth':
        width = Math.max(50, width - 10);
        break;
      case 'increaseHeight':
        height += 10;
        break;
      case 'decreaseHeight':
        height = Math.max(50, height - 10);
        break;
    }

    photo.style.width = width + 'px';
    photo.style.height = height + 'px';
  }
</script>

</body>
</html>
