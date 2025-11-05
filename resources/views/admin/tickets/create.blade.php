@extends('layouts.admin')

@section('title', 'Tambah Ticket (Admin)')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Tambah Ticket</h1>

  @if ($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-xl">
    @csrf

    <div>
      <label class="block mb-1 font-medium">Nama</label>
      <input type="text" name="nama" value="{{ old('nama', $prefill['nama'] ?? '') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">NIM</label>
      <input type="text" name="nim" value="{{ old('nim', $prefill['nim'] ?? '') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Program Studi</label>
      <input type="text" name="program_studi" value="{{ old('program_studi', $prefill['program_studi'] ?? '') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Asal Kampus</label>
      <input type="text" name="kampus" value="{{ old('kampus', $prefill['kampus'] ?? '') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Tahun Ajaran</label>
      <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $prefill['tahun_ajaran'] ?? '') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Judul Penelitian</label>
      <input type="text" name="judul_penelitian" value="{{ old('judul_penelitian') }}" class="w-full border rounded px-3 py-2" required>
    </div>
    @error('judul_penelitian')
      <div class="text-red-600 text-sm -mt-3">{{ $message }}</div>
    @enderror

    <div>
      <label class="block mb-1 font-medium">Keterangan (File yang diminta apa saja tulis disini)</label>
      <textarea name="keterangan" rows="3" class="w-full border rounded px-3 py-2" placeholder="Opsional">{{ old('keterangan') }}</textarea>
    </div>

    <div>
      <label for="lokasi_pengadilan" class="block mb-1 font-medium">Lokasi Pengadilan</label>
      <select name="lokasi_pengadilan" id="lokasi_pengadilan" class="w-full border rounded px-3 py-2">
        @php
          $opsi = ['PTA Gorontalo','PA Gorontalo','PA Suwawa','PA Limboto','PA Tilamuta','PA Kwandang','PA Marisa'];
          $selected = old('lokasi_pengadilan', $prefill['lokasi_pengadilan'] ?? 'PTA Gorontalo');
        @endphp
        @foreach($opsi as $op)
          <option value="{{ $op }}" {{ $selected === $op ? 'selected' : '' }}>{{ $op }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block mb-1 font-medium">Upload Surat (PDF, bisa banyak, max 20MB/file)</label>
      <input type="file" name="surat_files[]" accept="application/pdf" class="w-full border rounded px-3 py-2" multiple>
      <p class="text-xs text-gray-500 mt-1">Kamu bisa memilih beberapa file sekaligus.</p>
    </div>

    <div>
      <label class="block mb-1 font-medium">Upload Lampiran (PDF, opsional, bisa banyak, max 20MB/file)</label>
      <input type="file" name="lampiran_files[]" accept="application/pdf" class="w-full border rounded px-3 py-2" multiple>
    </div>

    <div class="pt-2 flex gap-3">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Ticket</button>
      <a href="{{ route('admin.tickets.index') }}" class="px-4 py-2 rounded border">Batal</a>
    </div>
  </form>
@endsection
