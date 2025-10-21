<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
   <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>
<body class="bg-gray-100 text-gray-900 flex">

    <!-- Sidebar User -->
<aside class="w-64 bg-blue-700 text-white min-h-screen p-5">
    <h1 class="text-2xl font-bold mb-6">Web Penelitian</h1>
    <nav class="space-y-3">
        <a href="{{ route('dashboard') }}" class="block hover:bg-blue-600 px-3 py-2 rounded">Dashboard</a>
        <a href="{{ route('profile.edit') }}" class="block hover:bg-blue-600 px-3 py-2 rounded">Profile</a>
        <a href="{{ route('tickets.create') }}" class="block hover:bg-blue-600 px-3 py-2 rounded">Tambah Data</a>
        <a href="{{ route('tickets.index') }}" class="block hover:bg-blue-600 px-3 py-2 rounded">Lihat Data</a>

        {{-- Tambahkan menu Update Password khusus user --}}
        @if(Auth::user()->role === 'user')
            <a href="{{ route('password.edit') }}" class="block hover:bg-blue-600 px-3 py-2 rounded">Update Password</a>
        @endif

        <form action="{{ route('logout') }}" method="POST" class="mt-6">
            @csrf
            <button type="submit" class="w-full text-left hover:bg-red-600 px-3 py-2 rounded bg-red-500">
                Logout
            </button>
        </form>
    </nav>
</aside>

{{-- Flash messages --}}
@if(session('success'))
  <div class="bg-green-500 text-white p-3 rounded mb-4">
      {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="bg-red-500 text-white p-3 rounded mb-4">
      {{ session('error') }}
  </div>
@endif

    <!-- Konten Utama -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</body>
</html>
