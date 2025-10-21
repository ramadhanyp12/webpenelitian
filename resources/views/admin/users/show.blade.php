@extends('layouts.admin')

@section('title', 'Detail User')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Detail User</h1>

  <div class="bg-white shadow rounded p-5 space-y-2">
    <div><span class="font-semibold">Nama:</span> {{ $user->name }}</div>
    <div><span class="font-semibold">Email:</span> {{ $user->email }}</div>
    <div><span class="font-semibold">Role:</span> {{ $user->role ?? '-' }}</div>
    <div><span class="font-semibold">NIM:</span> {{ optional($user->profile)->nim ?? '-' }}</div>
    <div><span class="font-semibold">Program Studi:</span> {{ optional($user->profile)->program_studi ?? '-' }}</div>
    <div><span class="font-semibold">Asal Kampus:</span> {{ optional($user->profile)->kampus ?? '-' }}</div>
    <div><span class="font-semibold">Tahun Ajaran:</span> {{ optional($user->profile)->tahun_ajaran ?? '-' }}</div>
  </div>

  <h2 class="text-xl font-semibold mt-8 mb-3">Ticket User</h2>
  <div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full border">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border">#</th>
          <th class="px-4 py-2 border">Judul</th>
          <th class="px-4 py-2 border">Status</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($user->tickets as $t)
          <tr>
            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
            <td class="px-4 py-2 border">{{ $t->judul_penelitian }}</td>
            <td class="px-4 py-2 border">{{ $t->status }}</td>
            <td class="px-4 py-2 border">
              <a href="{{ route('admin.tickets.show', $t->id) }}" class="text-blue-600">Lihat</a> |
              <a href="{{ route('admin.tickets.edit', $t->id) }}" class="text-yellow-600">Edit</a>
              
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Belum ada ticket</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border">Kembali</a>
  </div>
@endsection
