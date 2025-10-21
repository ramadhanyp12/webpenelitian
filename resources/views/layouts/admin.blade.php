<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name')) - Admin</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 text-gray-900 flex">

    <!-- Sidebar Admin -->
    <aside class="w-64 bg-gray-800 text-white min-h-screen p-5">
        <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>

        <nav class="space-y-3">
            <a href="{{ route('admin.dashboard') }}"
               class="block hover:bg-gray-700 px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
               Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="block hover:bg-gray-700 px-3 py-2 rounded">
            Data User
            </a>
            <a href="{{ route('admin.tickets.create') }}"
               class="block hover:bg-gray-700 px-3 py-2 rounded {{ request()->routeIs('admin.tickets.create') ? 'bg-gray-700' : '' }}">
               Tambah Tiket
            </a>

            <a href="{{ route('admin.tickets.index') }}"
               class="block hover:bg-gray-700 px-3 py-2 rounded {{ request()->routeIs('admin.tickets.*') ? 'bg-gray-700' : '' }}">
               Lihat Tiket User
            </a>

            <a href="{{ route('admin.approvals.index') }}" class="block hover:bg-gray-700 px-3 py-2 rounded">
            Approvals
            </a>
            
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
