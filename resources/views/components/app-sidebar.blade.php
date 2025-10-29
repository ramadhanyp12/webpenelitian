@php
// Pakai array biar rapi; ganti route() sesuai punyamu
$menus = [
  ['title' => 'Dashboard',      'icon' => 'home',    'href' => route('dashboard'),          'active' => request()->routeIs('dashboard')],
  ['title' => 'Profile',        'icon' => 'user',    'href' => route('profile.edit'),       'active' => request()->routeIs('profile.*')],
  ['title' => 'Tambah Data',    'icon' => 'plus',    'href' => route('tickets.create'),     'active' => request()->routeIs('tickets.create')],
  ['title' => 'Lihat Data',     'icon' => 'list',    'href' => route('tickets.index'),      'active' => request()->routeIs('tickets.index')],
  ['title' => 'Update Password','icon' => 'lock',    'href' => route('password.edit'),      'active' => request()->routeIs('password.*')],
];
@endphp

<aside
  class="bg-[#0E46A3] text-white h-screen sticky top-0 transition-all duration-300"
  :class="sidebarOpen ? 'w-64' : 'w-16'">

  <!-- Brand / Judul -->
  <div class="h-14 flex items-center px-4 border-b border-white/10">
    <span class="font-bold tracking-wide truncate" x-show="sidebarOpen">Web Penelitian</span>
    <span class="font-bold tracking-wide" x-show="!sidebarOpen">WP</span>
  </div>

  <!-- Menu -->
  <nav class="py-3 space-y-1">
    @foreach ($menus as $m)
      <a href="{{ $m['href'] }}"
         class="group flex items-center gap-3 px-3 py-2 rounded-r-full transition
                hover:bg-white/10 {{ $m['active'] ? 'bg-white/20' : '' }}"
         title="{{ $m['title'] }}">
        {{-- Icon (Heroicons outline SVG sederhana) --}}
        @switch($m['icon'])
          @case('home')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M3 12l9-8 9 8M4 10v10h6V14h4v6h6V10"/></svg>
          @break
          @case('user')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M16 14a4 4 0 10-8 0m-4 6a8 8 0 1116 0"/></svg>
          @break
          @case('plus')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
          @break
          @case('list')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
          @break
          @case('lock')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M12 11v3m-6 4h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2zm2-9a4 4 0 118 0v2H8V9z"/></svg>
          @break
        @endswitch

        <span class="truncate" x-show="sidebarOpen">{{ $m['title'] }}</span>
      </a>
    @endforeach
  </nav>
</aside>
