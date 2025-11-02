@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

  <div class="bg-white shadow rounded p-6">
    <p>Selamat datang, {{ Auth::user()->name }}! 🎉</p>
    <p class="mt-2 text-gray-600">Gunakan sidebar untuk mengelola tiket dan approvals.</p>
  </div>
  <div class="mt-8">
  <h2 class="text-xl font-semibold mb-3">🎥 Tutorial Penggunaan Website {{ Auth::user()->role === 'admin' ? 'Admin' : 'User' }}</h2>

  <div class="w-full max-w-3xl mx-auto">
    <div class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">
      <iframe
        src="https://www.youtube-nocookie.com/embed/cu4w7lWj5R4?rel=0&modestbranding=1"
        title="Tutorial Penggunaan Website"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen
        class="absolute top-0 left-0 w-full h-full">
      </iframe>
    </div>
  </div>
</div>

@endsection
