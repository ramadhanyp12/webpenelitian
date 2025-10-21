@extends('layouts.app')

@section('title', 'Edit Ticket #'.$ticket->id)

@section('content')
  <h1 class="text-2xl font-bold mb-6">Edit Ticket #{{ $ticket->id }}</h1>

  @if ($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-xl">
    @csrf
    @method('PUT')

    <div>
      <label class="block mb-1 font-medium">Nama</label>
      <input type="text" name="nama" value="{{ old('nama', $ticket->nama) }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">NIM</label>
      <input type="text" name="nim" value="{{ old('nim', $ticket->nim) }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Program Studi</label>
      <input type="text" name="program_studi" value="{{ old('program_studi', $ticket->program_studi) }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Asal Kampus</label>
      <input type="text" name="kampus" value="{{ old('kampus', $ticket->kampus) }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Tahun Ajaran</label>
      <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $ticket->tahun_ajaran) }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Judul Penelitian</label>
      <input type="text" name="judul_penelitian" value="{{ old('judul_penelitian', $ticket->judul_penelitian) }}" class="w-full border rounded px-3 py-2" required>
    </div>
    @error('judul_penelitian')
  <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
@enderror

    <div>
      <label class="block mb-1 font-medium">Keterangan (File yang diminta apa saja tulis disini)</label>
      <textarea name="keterangan" rows="3" class="w-full border rounded px-3 py-2">{{ old('keterangan', $ticket->keterangan) }}</textarea>
    </div>

    <div>
      <label for="lokasi_pengadilan" class="block mb-1 font-medium">Lokasi Pengadilan</label>
<select name="lokasi_pengadilan" id="lokasi_pengadilan" class="w-full border rounded px-3 py-2">
    <option value="PTA Gorontalo" {{ old('lokasi_pengadilan', $prefill['lokasi_pengadilan'] ?? $ticket->lokasi_pengadilan ?? '') === 'PTA Gorontalo' ? 'selected' : '' }}>PTA Gorontalo</option>
    <option value="PA Gorontalo" {{ old('lokasi_pengadilan', $prefill['lokasi_pengadilan'] ?? $ticket->lokasi_pengadilan ?? '') === 'PA Gorontalo' ? 'selected' : '' }}>PA Gorontalo</option>
    <option value="PA Suwawa" {{ old('lokasi_pengadilan', $prefill['lokasi_pengadilan'] ?? $ticket->lokasi_pengadilan ?? '') === 'PA Suwawa' ? 'selected' : '' }}>PA Suwawa</option>
    <option value="PA Limboto" {{ old('lokasi_pengadilan', $prefill['lokasi_pengadilan'] ?? $ticket->lokasi_pengadilan ?? '') === 'PA Limboto' ? 'selected' : '' }}>PA Limboto</option>
    <option value="PA Tilamuta" {{ old('lokasi_pengadilan', $prefill['lokasi_pengadilan'] ?? $ticket->lokasi_pengadilan ?? '') === 'PA Tilamuta' ? 'selected' : '' }}>PA Tilamuta</option>
    <option value="PA Kwandang" {{ old('lokasi_pengadilan', $prefill['lokasi_pengadilan'] ?? $ticket->lokasi_pengadilan ?? '') === 'PA Kwandang' ? 'selected' : '' }}>PA Kwandang</option>
    <option value="PA Marisa" {{ old('lokasi_pengadilan', $prefill['lokasi_pengadilan'] ?? $ticket->lokasi_pengadilan ?? '') === 'PA Marisa' ? 'selected' : '' }}>PA Marisa</option>
</select>

    </div>

    {{-- Daftar Surat --}}
    <div class="border rounded p-3 mb-4">
      <div class="font-semibold mb-2">Surat saat ini ({{ $ticket->suratDocuments->count() }})</div>
      @if($ticket->suratDocuments->count())
        <ul class="list-disc ml-5 space-y-1">
          @foreach ($ticket->suratDocuments as $doc)
            <li class="flex items-center gap-2">
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                {{ $doc->original_name }}
              </a>
              <button type="submit"
        form="del-surat-{{ $doc->id }}"
        class="text-red-600 hover:underline text-sm"
        onclick="return confirm('Hapus dokumen ini?')">
  Hapus
</button>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-sm text-gray-500">Belum ada file.</div>
      @endif
    </div>

    {{-- Daftar Lampiran --}}
    <div class="border rounded p-3 mb-4">
      <div class="font-semibold mb-2">Lampiran saat ini ({{ $ticket->lampiranDocuments->count() }})</div>
      @if($ticket->lampiranDocuments->count())
        <ul class="list-disc ml-5 space-y-1">
          @foreach ($ticket->lampiranDocuments as $doc)
            <li class="flex items-center gap-2">
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-600 underline">
                {{ $doc->original_name }}
              </a>
              <button type="submit"
        form="del-lampiran-{{ $doc->id }}"
        class="text-red-600 hover:underline text-sm"
        onclick="return confirm('Hapus dokumen ini?')">
  Hapus
</button>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-sm text-gray-500">Belum ada file.</div>
      @endif
    </div>

    {{-- Upload Baru --}}
    <div>
      <label class="block mb-1 font-medium">Tambah Surat baru (PDF, max 20MB/file)</label>
      <input type="file" name="surat_files[]" accept="application/pdf" class="w-full border rounded px-3 py-2" multiple>
    </div>

    <div>
      <label class="block mb-1 font-medium">Tambah Lampiran baru (PDF, max 20MB/file)</label>
      <input type="file" name="lampiran_files[]" accept="application/pdf" class="w-full border rounded px-3 py-2" multiple>
    </div>

    <div class="pt-2 flex gap-3">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
      <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded border">Batal</a>
    </div>
  </form>
  {{-- Form DELETE tersembunyi untuk SURAT --}}
@foreach ($ticket->suratDocuments as $doc)
  <form id="del-surat-{{ $doc->id }}"
        action="{{ route('tickets.documents.destroy', [$ticket->id, $doc->id]) }}"
        method="POST" style="display:none;">
    @csrf
    @method('DELETE')
  </form>
@endforeach

{{-- Form DELETE tersembunyi untuk LAMPIRAN --}}
@foreach ($ticket->lampiranDocuments as $doc)
  <form id="del-lampiran-{{ $doc->id }}"
        action="{{ route('tickets.documents.destroy', [$ticket->id, $doc->id]) }}"
        method="POST" style="display:none;">
    @csrf
    @method('DELETE')
  </form>
@endforeach
@endsection
