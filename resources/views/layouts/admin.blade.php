<!DOCTYPE html>
<html lang="en"
      x-data="{ open: window.innerWidth >= 1024 }"
      x-init="window.addEventListener('resize', () => open = window.innerWidth >= 1024)">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', config('app.name')) - Admin</title>
  @vite('resources/css/app.css')
  @vite('resources/js/app.js')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">

  {{-- NAVBAR: mobile full, desktop setelah sidebar --}}
  <header class="fixed top-0 right-0 left-0 h-12 bg-green-600 text-white z-50 transition-all duration-200 lg:left-64">
    <div class="h-full px-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button @click="open = !open" class="p-2 rounded hover:bg-green-700 focus:outline-none lg:hidden">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <span class="font-semibold text-sm truncate">@yield('title','Dashboard Admin')</span>
      </div>

      <div class="relative" x-data="{ drop:false }">
        <button @click="drop=!drop" class="flex items-center gap-2 px-2 py-1 rounded hover:bg-green-700">
          <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6z"/>
            </svg>
          </div>
          <span class="hidden sm:block text-sm">{{ Auth::user()->name ?? 'Admin' }}</span>
        </button>

        <div x-show="drop" x-cloak @click.outside="drop=false"
             class="absolute right-0 mt-2 w-56 bg-white text-gray-800 rounded shadow-lg py-2 z-50">
          <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-gray-100 text-sm">Dashboard</a>
          <form action="{{ route('logout') }}" method="POST" class="mt-1">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 text-sm">Sign out</button>
          </form>
        </div>
      </div>
    </div>
  </header>

  {{-- SIDEBAR --}}
  <aside
    class="fixed inset-y-0 left-0 z-40 bg-gray-800 text-white transition-transform duration-200 transform lg:transform-none lg:w-64 w-64"
    :class="open ? 'translate-x-0' : '-translate-x-full'">
    <div class="px-5 pt-14">
      {{-- Judul diturunkan & sejajar ikon --}}
      <div class="flex items-center mb-6 mt-1">
        <span class="text-2xl font-bold leading-none">Admin Panel</span>
      </div>

      <nav class="space-y-1.5 text-[15px]">
  {{-- Dashboard --}}
  <a href="{{ route('admin.dashboard') }}"
     class="group flex items-center gap-3.5 px-3.5 py-3 rounded-lg font-medium transition
            hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30
            {{ request()->routeIs('admin.dashboard') ? 'bg-white/15' : '' }}">
    <span class="shrink-0 w-5 h-5 inline-flex items-center justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
           viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l9 8h-3v9H6V11H3l9-8z"/></svg>
    </span>
    <span class="truncate">Dashboard</span>
  </a>

  {{-- Data User --}}
  <a href="{{ route('admin.users.index') }}"
     class="group flex items-center gap-3.5 px-3.5 py-3 rounded-lg font-medium transition
            hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30
            {{ request()->routeIs('admin.users.*') ? 'bg-white/15' : '' }}">
    <span class="shrink-0 w-5 h-5 inline-flex items-center justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
           viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.7 0 3-1.8 3-4s-1.3-4-3-4-3 1.8-3 4 1.3 4 3 4zM8 11c1.7 0 3-1.8 3-4S9.7 3 8 3 5 4.8 5 7s1.3 4 3 4zm0 2c-2.7 0-8 1.3-8 4v3h16v-3c0-2.7-5.3-4-8-4zm8 0c-.5 0-1 .1-1.6.2 1.8.8 3.6 2 3.6 3.8v3h6v-3c0-2.7-5.3-4-8-4z"/></svg>
    </span>
    <span class="truncate">Data User</span>
  </a>

  {{-- Tambah Tiket --}}
  <a href="{{ route('admin.tickets.create') }}"
     class="group flex items-center gap-3.5 px-3.5 py-3 rounded-lg font-medium transition
            hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30
            {{ request()->routeIs('admin.tickets.create') ? 'bg-white/15' : '' }}">
    <span class="shrink-0 w-5 h-5 inline-flex items-center justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
           viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"/></svg>
    </span>
    <span class="truncate">Tambah Tiket</span>
  </a>

  {{-- Lihat Tiket User --}}
  <a href="{{ route('admin.tickets.index') }}"
     class="group flex items-center gap-3.5 px-3.5 py-3 rounded-lg font-medium transition
            hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30
            {{ request()->routeIs('admin.tickets.index') ? 'bg-white/15' : '' }}">
    <span class="shrink-0 w-5 h-5 inline-flex items-center justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
           viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/></svg>
    </span>
    <span class="truncate">Lihat Tiket User</span>
  </a>

  {{-- Approvals --}}
  <a href="{{ route('admin.approvals.index') }}"
     class="group flex items-center gap-3.5 px-3.5 py-3 rounded-lg font-medium transition
            hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30
            {{ request()->routeIs('admin.approvals.*') ? 'bg-white/15' : '' }}">
    <span class="shrink-0 w-5 h-5 inline-flex items-center justify-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
           viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H8a2 2 0 00-2 2v16l6-3 6 3V4a2 2 0 00-2-2z"/></svg>
    </span>
    <span class="truncate">Approvals</span>
  </a>

  {{-- Logout --}}
  <form action="{{ route('logout') }}" method="POST" class="pt-1.5">
    @csrf
    <button type="submit"
            class="group w-full flex items-center gap-3.5 px-3.5 py-3 rounded-lg font-medium transition
                   bg-white/10 hover:bg-white/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30">
      <span class="shrink-0 w-5 h-5 inline-flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
             viewBox="0 0 24 24" fill="currentColor"><path d="M10 17l5-5-5-5v10zM4 4h6v2H6v12h4v2H4z"/></svg>
      </span>
      <span class="truncate">Logout</span>
    </button>
  </form>
</nav>

    </div>
  </aside>

  {{-- Overlay mobile --}}
  <div x-show="open && window.innerWidth < 1024" x-cloak
       class="fixed inset-0 bg-black/40 z-30 lg:hidden"
       @click="open=false"></div>

  {{-- MAIN --}}
  <main class="pt-14 pb-16 transition-all duration-200 lg:ml-64">
    <div class="px-4">
      @if(session('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="bg-red-500 text-white p-3 rounded mb-4">{{ session('error') }}</div>
      @endif

      @yield('content')
    </div>
  </main>

  {{-- FOOTER: mobile full, desktop setelah sidebar --}}
  <footer class="fixed bottom-0 right-0 left-0 bg-white border-t text-xs text-gray-600 h-10 flex items-center px-4 z-40 transition-all duration-200 lg:left-64">
    <span class="hidden sm:inline">© {{ now()->year }} Web Penelitian (Admin).</span>
    <span class="sm:mx-4 mx-2">•</span>
    <span>WhatsApp: <a href="https://wa.me/6282145667305" target="_blank" class="text-green-600 hover:underline">082145667305</a></span>
  </footer>

</body>
</html>
