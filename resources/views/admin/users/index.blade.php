@extends('layouts.admin')

@section('title','Data User')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Data User</h1>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 border border-green-300 p-3 rounded mb-4">{{ session('success') }}</div>
  @endif

  <div class="overflow-x-auto bg-white shadow rounded">
    <table class="min-w-full border border-gray-300">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border">#</th>
          <th class="px-4 py-2 border">Nama</th>
          <th class="px-4 py-2 border">Email</th>
          <th class="px-4 py-2 border">Asal Kampus</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          <tr class="border-t">
            <td class="px-4 py-2 border">{{ ($users->currentPage()-1)*$users->perPage() + $loop->iteration }}</td>
            <td class="px-4 py-2 border">{{ $u->name }}</td>
            <td class="px-4 py-2 border">{{ $u->email }}</td>
            <td class="px-4 py-2 border">{{ $u->profile->kampus ?? '-' }}</td>
            <td class="px-4 py-2 border space-x-2">
              <a href="{{ route('admin.users.show', $u->id) }}" class="text-blue-600">Lihat</a> |
              <a href="{{ route('admin.users.edit', $u->id) }}" class="text-yellow-600">Edit</a>
              @if($u->role !== 'admin')
    <span class="mx-1">|</span>
    <form action="{{ route('admin.users.destroy', $u->id) }}"
          method="POST"
          class="inline"
          onsubmit="return confirm('Hapus user ini BESERTA seluruh tiket, approval, dan dokumennya?');">
      @csrf
      @method('DELETE')
      <button type="submit" class="text-red-600 hover:underline">Hapus</button>
    </form>
  @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada user</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $users->links() }}
  </div>
@endsection
