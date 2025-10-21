@extends('layouts.admin')

@section('title', 'Daftar Tickets (Admin)')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Daftar Tickets</h1>

  @if (session('success'))
    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  <div class="mb-4">
    <a href="{{ route('admin.tickets.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">+ Buat Ticket</a>
  </div>

  <div class="overflow-x-auto bg-white shadow rounded">
    <table class="min-w-full border border-gray-300">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border">#</th>
          <th class="px-4 py-2 border">User</th>
          <th class="px-4 py-2 border">Judul</th>
          <th class="px-4 py-2 border">Status</th>
          <th class="px-4 py-2 border">Dokumen</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($tickets as $t)
          <tr class="border-t">
            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
            <td class="px-4 py-2 border">{{ $t->user->name }}</td>
            <td class="px-4 py-2 border">{{ $t->judul_penelitian }}</td>
            <td class="px-4 py-2 border">
  @if ($t->status === 'ditolak' && filled($t->alasan_ditolak))
    <span class="inline-block rounded border border-red-300 bg-red-50 text-red-700 text-xs px-2 py-1">
      Ditolak: {{ $t->alasan_ditolak }}
    </span>
  @else
    {{ $t->status }}
  @endif
</td>

            {{-- DOKUMEN --}}
            <td class="px-4 py-2 border">
              {{-- daftar surat/lampiran --}}
              @foreach($t->documents as $doc)
                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                  {{ $doc->original_name }}
                </a>@if(!$loop->last), @endif
              @endforeach

              {{-- link Hasil (opsional) --}}
              @if ($t->hasil_pdf_path)
                <span class="text-gray-400 mx-2">|</span>
                <a href="{{ asset('storage/'.$t->hasil_pdf_path) }}" target="_blank" class="text-blue-600 underline">
                  Hasil
                </a>
              @endif
            </td>

            <td class="px-4 py-2 border">
              <a href="{{ route('admin.tickets.show', $t->id) }}" class="text-blue-600">Lihat</a> |
              <a href="{{ route('admin.tickets.edit', $t->id) }}" class="text-yellow-600">Edit</a>
              {{-- Hapus --}}
  <span class="mx-1">|</span>
  <form action="{{ route('admin.tickets.destroy', $t->id) }}"
        method="POST"
        class="inline"
        onsubmit="return confirm('Hapus ticket ini beserta semua dokumennya?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
  </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-2 text-center text-gray-500">Belum ada ticket</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
