@extends('layouts.admin')

@section('title', 'Detail Ticket (Admin)')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Detail Ticket #{{ $ticket->id }}</h1>

  <div class="bg-white shadow rounded p-6 space-y-4">
    <div>
      <span class="font-semibold">Nama:</span>
      <span>{{ $ticket->nama }}</span>
    </div>
    <div>
      <span class="font-semibold">NIM:</span>
      <span>{{ $ticket->nim }}</span>
    </div>
    <div>
      <span class="font-semibold">Program Studi:</span>
      <span>{{ $ticket->program_studi }}</span>
    </div>
    <div>
      <span class="font-semibold">Kampus:</span>
      <span>{{ $ticket->kampus }}</span>
    </div>
    <div>
      <span class="font-semibold">Tahun Ajaran:</span>
      <span>{{ $ticket->tahun_ajaran }}</span>
    </div>
    <div>
      <span class="font-semibold">Judul Penelitian:</span>
      <span>{{ $ticket->judul_penelitian }}</span>
    </div>
    <div>
      <span class="font-semibold">Keterangan:</span>
      <span>{{ $ticket->keterangan ?? '-' }}</span>
    </div>
    <div>
      <span class="font-semibold">Lokasi Pengadilan:</span>
      <span>{{ $ticket->lokasi_pengadilan ?? '-' }}</span>
    </div>
    <div>
      <span class="font-semibold">Status:</span>
      <span class="px-2 py-1 rounded bg-gray-200">{{ $ticket->status }}</span>
    </div>

    {{-- Dokumen Surat --}}
    <div>
      <h2 class="font-semibold mb-2">Surat</h2>
      @if($ticket->suratDocuments->count())
        <ul class="list-disc ml-5 space-y-1">
          @foreach ($ticket->suratDocuments as $doc)
            <li>
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                {{ $doc->original_name }}
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-sm text-gray-500">Belum ada file surat.</div>
      @endif
    </div>

    {{-- Dokumen Lampiran --}}
    <div>
      <h2 class="font-semibold mb-2">Lampiran</h2>
      @if($ticket->lampiranDocuments->count())
        <ul class="list-disc ml-5 space-y-1">
          @foreach ($ticket->lampiranDocuments as $doc)
            <li>
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                {{ $doc->original_name }}
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-sm text-gray-500">Belum ada file lampiran.</div>
      @endif
    </div>

    <div class="pt-4">
      <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Edit</a>
      <form action="{{ route('admin.tickets.destroy', $ticket->id) }}"
        method="POST"
        class="inline"
        onsubmit="return confirm('Yakin hapus Ticket #{{ $ticket->id }} beserta semua dokumennya?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Hapus</button>
  </form>
      <a href="{{ route('admin.tickets.index') }}" class="px-4 py-2 rounded border">Kembali</a>
    </div>
  </div>
@endsection
