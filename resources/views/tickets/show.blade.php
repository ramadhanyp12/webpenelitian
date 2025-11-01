@extends('layouts.app')

@section('title', 'Detail Ticket #'.$ticket->id)

@section('content')
  <h1 class="text-2xl font-bold mb-6">Detail Ticket</h1>

  <div class="bg-white rounded shadow p-4 mb-6">
    <div class="grid md:grid-cols-2 gap-4">
      <div><strong>ID:</strong> {{ $ticket->id }}</div>
      <div><strong>Nama:</strong> {{ $ticket->nama }}</div>
      <div><strong>NIM:</strong> {{ $ticket->nim }}</div>
      <div><strong>Program Studi:</strong> {{ $ticket->program_studi }}</div>
      <div><strong>Kampus:</strong> {{ $ticket->kampus }}</div>
      <div><strong>Tahun Ajaran:</strong> {{ $ticket->tahun_ajaran }}</div>
      <div class="md:col-span-2"><strong>Judul Penelitian:</strong> {{ $ticket->judul_penelitian }}</div>
      <p><strong>Lokasi Pengadilan:</strong> {{ $ticket->lokasi_pengadilan ?: '-' }}</p>
      <div class="md:col-span-2"><strong>Status:</strong> {{ $ticket->status }}</div>
      <div class="mt-6">
  <h3 class="font-semibold mb-2">Keterangan (File yang diminta apa saja tulis disini)</h3>
  <div class="border rounded px-4 py-3 bg-white">
    {!! nl2br(e($ticket->keterangan ?: 'Tidak ada keterangan')) !!}
  </div>
</div>
      @if($ticket->status === 'ditolak' && $ticket->alasan_ditolak)
        <div class="md:col-span-2">
          <span class="inline-block bg-red-100 text-red-700 border border-red-300 px-3 py-1 rounded">
            Ditolak: {{ $ticket->alasan_ditolak }}
          </span>
        </div>
      @endif
    </div>
  </div>

  <div class="bg-white rounded shadow p-4">
    <h2 class="text-lg font-semibold mb-3">Dokumen</h2>

    {{-- Surat --}}
    <div class="mb-3">
      <div class="font-medium">Surat Permohonan:</div>
      @if($ticket->suratDocuments->count())
        <ul class="list-disc ml-5">
          @foreach($ticket->suratDocuments as $doc)
            <li>
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                {{ $doc->original_name }}
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-gray-500">Tidak ada surat</div>
      @endif
    </div>

    {{-- Lampiran --}}
    <div class="mb-3">
      <div class="font-medium">Lampiran Permohonan:</div>
      @if($ticket->lampiranDocuments->count())
        <ul class="list-disc ml-5">
          @foreach($ticket->lampiranDocuments as $doc)
            <li>
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                {{ $doc->original_name }}
              </a>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-gray-500">Tidak ada lampiran</div>
      @endif
    </div>

    {{-- Hasil final (opsional) --}}
    <div>
      <div class="font-medium">Surat Izin:</div>
      @if($ticket->hasil_pdf_path)
        <a href="{{ asset('storage/'.$ticket->hasil_pdf_path) }}" target="_blank" class="text-blue-600 underline">
          Lihat hasil
        </a>
      @else
        <div class="text-gray-500">Belum ada hasil</div>
      @endif
    </div>
  </div>

  <div class="mt-4 flex gap-3">
    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded border">← Kembali</a>
    <a href="{{ route('tickets.edit', $ticket->id) }}" class="px-4 py-2 rounded bg-blue-600 text-white">Edit Ticket</a>
  </div>
@endsection
