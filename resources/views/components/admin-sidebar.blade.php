@php
$menus = [
  ['title' => 'Dashboard', 'icon' => 'home', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
  ['title' => 'Tickets',   'icon' => 'list', 'href' => route('admin.tickets.index'), 'active' => request()->routeIs('admin.tickets.*')],
  ['title' => 'Approvals', 'icon' => 'check','href' => route('admin.approvals.index'), 'active' => request()->routeIs('admin.approvals.*')],
  ['title' => 'Users',     'icon' => 'users','href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
];
@endphp

<aside class="bg-[#0E46A3] text-white h-screen sticky top-0 transition-all duration-300"
       :class="sidebarOpen ? 'w-64' : 'w-16'">
  <div class="h-14 flex items-center px-4 border-b border-white/10">
    <span class="font-bold tracking-wide truncate" x-show="sidebarOpen">Admin Panel</span>
    <span class="font-bold tracking-wide" x-show="!sidebarOpen">AD</span>
  </div>

  <nav class="py-3 space-y-1">
    @foreach ($menus as $m)
      <a href="{{ $m['href'] }}"
         class="group flex items-center gap-3 px-3 py-2 rounded-r-full transition
                hover:bg-white/10 {{ $m['active'] ? 'bg-white/20' : '' }}"
         title="{{ $m['title'] }}">
        @switch($m['icon'])
          @case('home')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M3 12l9-8 9 8M4 10v10h6V14h4v6h6V10"/></svg>
          @break
          @case('list')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
          @break
          @case('check')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M5 13l4 4L19 7"/></svg>
          @break
          @case('users')
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m9-10a4 4 0 100-8 4 4 0 000 8m7 4v-1a3 3 0 00-3-3"/></svg>
          @break
        @endswitch

        <span class="truncate" x-show="sidebarOpen">{{ $m['title'] }}</span>
      </a>
    @endforeach
  </nav>
</aside>
