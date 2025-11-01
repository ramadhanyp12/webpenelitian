@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold">Halo {{ Auth::user()->name }}</h1>
    <p>Anda login sebagai <strong>{{ Auth::user()->role }}</strong></p>
    <br><br>
    <h2 class="text-xl font-semibold mb-3">🎥 Tutorial Penggunaan Website User</h2>
    <div class="w-full max-w-3xl mx-auto">
  <div class="relative pt-[56.25%] overflow-hidden rounded-[12px] shadow">
    <iframe
      class="absolute inset-0 w-full h-full"
      src="https://www.youtube-nocookie.com/embed/hP_WmfwAx8Y?rel=0&modestbranding=1"
      title="Tutorial Penggunaan Website User"
      loading="lazy"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
      referrerpolicy="strict-origin-when-cross-origin"
      allowfullscreen>
    </iframe>
  </div>
</div>

@endsection
