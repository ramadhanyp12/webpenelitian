@extends('layouts.admin')

@section('title', 'Edit Ticket (Admin)')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Edit Ticket #{{ $ticket->id }}</h1>

  @if (session('success'))
    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- ============ BLOK DAFTAR DOKUMEN (DI LUAR FORM UTAMA) ============ --}}
  <div class="space-y-4 mb-6">
    <div class="border rounded p-3">
      <div class="font-semibold mb-2">Surat permohonan saat ini ({{ $ticket->suratDocuments()->count() }})</div>
      @if($ticket->suratDocuments()->count())
        <ul class="list-disc ml-5 space-y-1">
          @foreach ($ticket->suratDocuments as $doc)
            <li class="flex items-center gap-2">
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                {{ $doc->original_name }}
              </a>
              {{-- form HAPUS STANDALONE (bukan di dalam form update) --}}
              <form action="{{ route('admin.tickets.documents.destroy', [$ticket->id, $doc->id]) }}"
                    method="POST" onsubmit="return confirm('Hapus dokumen ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button class="text-red-600 hover:underline">Hapus</button>
              </form>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-sm text-gray-500">Belum ada file.</div>
      @endif
    </div>

    <div class="border rounded p-3">
      <div class="font-semibold mb-2">Lampiran permohonan saat ini ({{ $ticket->lampiranDocuments()->count() }})</div>
      @if($ticket->lampiranDocuments()->count())
        <ul class="list-disc ml-5 space-y-1">
          @foreach ($ticket->lampiranDocuments as $doc)
            <li class="flex items-center gap-2">
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                {{ $doc->original_name }}
              </a>
              {{-- form HAPUS STANDALONE --}}
              <form action="{{ route('admin.tickets.documents.destroy', [$ticket->id, $doc->id]) }}"
                    method="POST" onsubmit="return confirm('Hapus dokumen ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button class="text-red-600 hover:underline">Hapus</button>
              </form>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-sm text-gray-500">Belum ada file.</div>
      @endif
    </div>
  </div>

{{-- Surat Izin (mendukung kolom baru & lama) --}}
@php
  $suratPath = $ticket->surat_izin_pdf_path ?: $ticket->hasil_pdf_path;   // path yg ditampilkan
  $suratCol  = $ticket->surat_izin_pdf_path ? 'surat_izin_pdf_path'
             : ($ticket->hasil_pdf_path ? 'hasil_pdf_path' : null);       // nama kolom yg dihapus
@endphp

<div class="mb-4">
  <div class="font-semibold">
    Surat Izin {{ $ticket->surat_izin_pdf_path ? '' : '(data lama)' }}
  </div>

  @if($suratPath)
    <a href="{{ asset('storage/'.$suratPath) }}" target="_blank" class="text-blue-600 underline">
      Lihat
    </a>

    <form action="{{ route('admin.tickets.suratizin.destroy', $ticket->id) }}"
          method="POST" class="inline ml-2"
          onsubmit="return confirm('Hapus file Surat Izin ini?');">
      @csrf
      @method('DELETE')
      <input type="hidden" name="col" value="{{ $suratCol }}">
      <button class="text-red-600 hover:underline">Hapus</button>
    </form>
  @else
    <div class="text-gray-500">Belum ada file Surat Izin.</div>
  @endif
</div>

  {{-- Hasil Penelitian saat ini --}}
<div class="mb-6">
  <div class="font-semibold mb-2">Hasil Penelitian saat ini</div>
  @if($ticket->hasil_pdf_path)
    <div class="flex items-center gap-3">
      <a href="{{ asset('storage/'.$ticket->hasil_pdf_path) }}"
         target="_blank"
         class="text-blue-600 underline">
        Lihat Hasil
      </a>

      <form action="{{ route('admin.tickets.hasil.destroy', $ticket->id) }}"
            method="POST"
            onsubmit="return confirm('Hapus file Hasil Penelitian ini?');"
            class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
      </form>
    </div>
  @else
    <div class="text-gray-500">Belum ada file Hasil Penelitian.</div>
  @endif
</div>

  {{-- ============ FORM UTAMA UPDATE (TANPA FORM LAIN DI DALAMNYA) ============ --}}
  <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-xl">
    @csrf
    @method('PUT')

    <div>
      <label class="block mb-1 font-medium">Judul Penelitian</label>
      <input type="text" name="judul_penelitian" value="{{ old('judul_penelitian', $ticket->judul_penelitian) }}"
             class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Status</label>
<select name="status" class="w-full border rounded px-3 py-2">
  @foreach (['dikirim', 'menunggu_persetujuan', 'disetujui', 'ditolak', 'menunggu_hasil', 'selesai'] as $s)
    <option value="{{ $s }}" @selected(old('status', $ticket->status) === $s)>{{ $s }}</option>
  @endforeach
</select>
    </div>

    <div>
      <label class="block mb-1 font-medium">Tambah Surat permohonan baru (PDF, bisa lebih dari satu, max 20MB/file)</label>
      <input type="file" name="surat_files[]" accept="application/pdf" class="w-full border rounded px-3 py-2" multiple>
    </div>

    <div>
      <label class="block mb-1 font-medium">Tambah Lampiran permohonan baru (PDF, bisa lebih dari satu, max 20MB/file)</label>
      <input type="file" name="lampiran_files[]" accept="application/pdf" class="w-full border rounded px-3 py-2" multiple>
    </div>

    <div>
      <label class="block mb-1 font-medium">Upload Surat Izin (PDF, max 20MB)</label>
      <input type="file" name="hasil_pdf" accept="application/pdf" class="w-full border rounded px-3 py-2">
      @if ($ticket->hasil_pdf_path)
        <p class="text-sm mt-1">
          File saat ini:
          <a href="{{ asset('storage/'.$ticket->hasil_pdf_path) }}" target="_blank" class="text-blue-600 underline">Lihat / unduh</a>
        </p>
      @endif
    </div>

    <div class="pt-2 flex gap-3">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      <a href="{{ route('admin.tickets.index') }}" class="px-4 py-2 rounded border">Batal</a>
    </div>
  </form>
@endsection
