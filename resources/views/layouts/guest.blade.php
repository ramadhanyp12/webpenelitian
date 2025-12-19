{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>@yield('title','Website izin penelitian')</title>

  {{-- Muat asset hasil build. Jika manifest belum ada, fallback ke @vite --}}
  @php
    $manifestPath = public_path('build/manifest.json');
  @endphp

  @if (file_exists($manifestPath))
    @php $manifest = json_decode(file_get_contents($manifestPath), true); @endphp
    <link rel="stylesheet" href="{{ asset('build/'.$manifest['resources/css/app.css']['file']) }}">
    <script type="module" src="{{ asset('build/'.$manifest['resources/js/app.js']['file']) }}"></script>
  @else
    @vite(['resources/css/app.css','resources/js/app.js'])
  @endif
</head>
<body class="min-h-screen bg-cover bg-center" style="background-image:url('{{ asset('images/pta.png') }}');">
  <div class="min-h-screen flex items-center justify-center bg-black/30 px-4">
    @yield('content')
  </div>
</body>
</html>
