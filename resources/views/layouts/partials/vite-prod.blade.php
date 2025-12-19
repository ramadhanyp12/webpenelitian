@php
  $manifestPath = public_path('build/manifest.json');
@endphp

@if (file_exists($manifestPath))
  @php $manifest = json_decode(file_get_contents($manifestPath), true); @endphp

  @if(isset($manifest['resources/css/app.css']['file']))
    <link rel="stylesheet" href="{{ asset('build/'.$manifest['resources/css/app.css']['file']) }}">
  @endif

  @if(isset($manifest['resources/js/app.js']['file']))
    <script type="module" src="{{ asset('build/'.$manifest['resources/js/app.js']['file']) }}"></script>
  @endif
@else
  {{-- fallback saat dev --}}
  @vite(['resources/css/app.css','resources/js/app.js'])
@endif

