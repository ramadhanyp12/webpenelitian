<header class="h-14 bg-[#0DA24A] text-white flex items-center justify-between px-4 shadow">
  <!-- Left: Hamburger -->
  <button @click="sidebarOpen = !sidebarOpen"
          class="p-2 rounded hover:bg-white/10 focus:outline-none"
          aria-label="Toggle Sidebar">
    <!-- hamburger -->
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>

  <!-- Center: judul halaman opsional -->
  <div class="font-semibold truncate">
    @yield('title', 'Web Penelitian')
  </div>

  <!-- Right: user dropdown -->
  <div class="relative" x-data="{open:false}">
    <button @click="open=!open" class="flex items-center gap-2 p-2 rounded hover:bg-white/10">
      <!-- avatar dummy -->
      <div class="w-8 h-8 rounded-full bg-white/20 grid place-items-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-width="1.8" d="M16 14a4 4 0 10-8 0m-4 6a8 8 0 1116 0"/>
        </svg>
      </div>
      <span class="hidden sm:block text-sm">{{ auth()->user()->name ?? 'User' }}</span>
    </button>

    <!-- dropdown -->
    <div x-cloak x-show="open" @click.outside="open=false"
         class="absolute right-0 mt-2 w-64 bg-white text-gray-800 rounded shadow-lg overflow-hidden z-50">
      <div class="bg-[#0DA24A] text-white px-4 py-4">
        <div class="text-sm opacity-80">{{ auth()->user()->email ?? '' }}</div>
        <div class="font-semibold truncate">{{ auth()->user()->name ?? '' }}</div>
      </div>
      <div class="p-3">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="w-full px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
            Sign out
          </button>
        </form>
      </div>
    </div>
  </div>
</header>
