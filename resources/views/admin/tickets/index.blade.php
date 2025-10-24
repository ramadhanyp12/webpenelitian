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
          {{-- Dokumen dibagi 4 kolom --}}
          <th class="px-4 py-2 border">Surat</th>
          <th class="px-4 py-2 border">Lampiran</th>
          <th class="px-4 py-2 border">Surat Izin</th>
          <th class="px-4 py-2 border">Hasil Penelitian</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @forelse ($tickets as $t)
          <tr class="border-t align-top">
            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
            <td class="px-4 py-2 border">{{ $t->user->name }}</td>
            <td class="px-4 py-2 border">{{ $t->judul_penelitian }}</td>

            {{-- STATUS: badge + alasan jika ditolak --}}
            <td class="px-4 py-2 border">
              @if ($t->status === 'ditolak' && filled($t->alasan_ditolak))
                <span class="inline-block rounded border border-red-300 bg-red-50 text-red-700 text-xs px-2 py-1">
                  Ditolak: {{ $t->alasan_ditolak }}
                </span>
              @else
                @php
                  $classes = [
                    'dikirim'                => 'bg-gray-100 text-gray-700',
                    'menunggu_persetujuan'   => 'bg-yellow-100 text-yellow-700',
                    'disetujui'              => 'bg-blue-100 text-blue-700',
                    'ditolak'                => 'bg-red-100 text-red-700',
                    'menunggu_hasil'         => 'bg-amber-100 text-amber-700',
                    'selesai'                => 'bg-green-100 text-green-700',
                  ];
                @endphp
                <span class="px-2 py-1 rounded text-sm {{ $classes[$t->status] ?? 'bg-gray-100 text-gray-700' }}">
                  {{ $t->status }}
                </span>
              @endif
            </td>

            {{-- SURAT --}}
            <td class="px-4 py-2 border">
              @php $surats = $t->suratDocuments ?? collect(); @endphp
              @if($surats->count())
                @foreach($surats as $doc)
                  <div>
                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                      {{ $doc->original_name }}
                    </a>
                  </div>
                @endforeach
              @else
                <span class="text-gray-500">-</span>
              @endif
            </td>

            {{-- LAMPIRAN --}}
            <td class="px-4 py-2 border">
              @php $lampirans = $t->lampiranDocuments ?? collect(); @endphp
              @if($lampirans->count())
                @foreach($lampirans as $doc)
                  <div>
                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                      {{ $doc->original_name }}
                    </a>
                  </div>
                @endforeach
              @else
                <span class="text-gray-500">-</span>
              @endif
            </td>

            {{-- SURAT IZIN (PDF hasil generate admin) --}}
            <td class="px-4 py-2 border">
              @if ($t->hasil_pdf_path)
                <a href="{{ asset('storage/'.$t->hasil_pdf_path) }}" target="_blank" class="text-blue-600 underline">
                  Surat Izin
                </a>
              @else
                <span class="text-gray-500">Belum ada</span>
              @endif
            </td>

            {{-- HASIL PENELITIAN (upload user) --}}
            <td class="px-4 py-2 border">
              @if ($t->hasil_penelitian_path)
                <a href="{{ asset('storage/'.$t->hasil_penelitian_path) }}" target="_blank" class="text-blue-600 underline">
                  Hasil
                </a>

                {{-- Hapus (opsional, butuh route admin.tickets.hasil.destroy) --}}
                <form action="{{ route('admin.tickets.hasil.destroy', $t) }}" method="POST"
                      onsubmit="return confirm('Hapus file hasil penelitian?')" class="inline-block ml-2">
                  @csrf @method('DELETE')
                  <button class="text-red-600 underline text-sm">Hapus</button>
                </form>
              @else
                <span class="text-gray-500">Belum upload</span>
              @endif
            </td>

            {{-- AKSI --}}
            <td class="px-4 py-2 border">
              <a href="{{ route('admin.tickets.show', $t->id) }}" class="text-blue-600">Lihat</a> |
              <a href="{{ route('admin.tickets.edit', $t->id) }}" class="text-yellow-600">Edit</a>
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
            <td colspan="9" class="px-4 py-2 text-center text-gray-500">Belum ada ticket</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
