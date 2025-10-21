@extends('layouts.admin')

@section('title','Edit Approval')

@section('content')
  <h1 class="text-2xl font-bold mb-6">
    Edit Approval – Ticket #{{ $approval->ticket->id }} ({{ $approval->ticket->user->name }})
  </h1>

  @if (session('success'))
    <div class="bg-green-100 text-green-800 border border-green-300 p-3 rounded mb-4">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.approvals.update', $approval->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Nama Mahasiswa</label>
        <input name="nama_mahasiswa" class="w-full border rounded px-3 py-2" value="{{ old('nama_mahasiswa', $approval->nama_mahasiswa) }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">NIM</label>
        <input name="nim_mahasiswa" class="w-full border rounded px-3 py-2" value="{{ old('nim_mahasiswa', $approval->nim_mahasiswa) }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">Program Studi</label>
        <input name="prodi_mahasiswa" class="w-full border rounded px-3 py-2" value="{{ old('prodi_mahasiswa', $approval->prodi_mahasiswa) }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">Judul Penelitian</label>
        <input name="judul_penelitian" class="w-full border rounded px-3 py-2" value="{{ old('judul_penelitian', $approval->judul_penelitian) }}" required>
      </div>
    </div>

    <hr class="my-2">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Nama Penandatangan</label>
        <input name="nama_penandatangan" class="w-full border rounded px-3 py-2" value="{{ old('nama_penandatangan', $approval->nama_penandatangan) }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">NIP Penandatangan</label>
        <input name="nip_penandatangan" class="w-full border rounded px-3 py-2" value="{{ old('nip_penandatangan', $approval->nip_penandatangan) }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">Pangkat/Gol</label>
        <input name="pangkat_gol" class="w-full border rounded px-3 py-2" value="{{ old('pangkat_gol', $approval->pangkat_gol) }}">
      </div>
      <div>
        <label class="block mb-1 font-medium">Jabatan Penandatangan</label>
        <input name="jabatan_penandatangan" class="w-full border rounded px-3 py-2" value="{{ old('jabatan_penandatangan', $approval->jabatan_penandatangan) }}">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Nomor Surat</label>
        <input name="nomor_surat" class="w-full border rounded px-3 py-2" value="{{ old('nomor_surat', $approval->nomor_surat) }}" required>
      </div>
      @error('nomor_surat')
  <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
@enderror
      <div>
        <label class="block mb-1 font-medium">Tanggal Surat</label>
        <input type="date" name="tanggal_surat" class="w-full border rounded px-3 py-2" value="{{ old('tanggal_surat', optional($approval->tanggal_surat)->format('Y-m-d')) }}" required>
      </div>
      <div class="md:col-span-2">
        <label class="block mb-1 font-medium">Tujuan (Kepada Yth.)</label>
        <input name="tujuan" class="w-full border rounded px-3 py-2" value="{{ old('tujuan', $approval->tujuan) }}" required>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Upload Tanda Tangan (png/jpg)</label>
        <input type="file" name="ttd" accept=".png,.jpg,.jpeg" class="w-full border rounded px-3 py-2">
        @if($approval->ttd_path)
          <div class="text-sm mt-1">Saat ini: <span class="text-gray-600">{{ $approval->ttd_path }}</span></div>
        @endif
      </div>
      <div>
        <label class="block mb-1 font-medium">Upload Stempel (png/jpg)</label>
        <input type="file" name="stempel" accept=".png,.jpg,.jpeg" class="w-full border rounded px-3 py-2">
        @if($approval->stempel_path)
          <div class="text-sm mt-1">Saat ini: <span class="text-gray-600">{{ $approval->stempel_path }}</span></div>
        @endif
      </div>
    </div>

    @if($approval->generated_pdf_path)
      <div class="text-sm">
        <span class="font-medium">PDF Terbuat:</span>
        <a href="{{ asset('storage/'.$approval->generated_pdf_path) }}" target="_blank" class="text-blue-600 underline">Lihat / unduh</a>
      </div>
    @endif

    <div class="flex flex-wrap items-center gap-3 pt-2">
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>

      <form action="{{ route('admin.approvals.generatePdf', $approval->id) }}" method="POST">
        @csrf
        <button class="bg-indigo-600 text-white px-4 py-2 rounded">Generate PDF</button>
      </form>

      <a href="{{ route('admin.approvals.index') }}" class="px-4 py-2 rounded border">Kembali</a>
    </div>
  </form>

  {{-- Blok khusus: Tolak dengan alasan dari halaman edit --}}
  <div class="max-w-2xl mt-6 p-4 border rounded bg-red-50">
    <h3 class="font-semibold text-red-700 mb-2">Tolak Tiket ini</h3>
    <form action="{{ route('admin.approvals.deny', $approval->ticket_id) }}" method="POST">
      @csrf
      <textarea name="alasan_ditolak" rows="3" class="w-full border rounded px-3 py-2"
                placeholder="Tulis alasan penolakan" required></textarea>
      <div class="mt-2">
        <button class="px-3 py-1 bg-red-700 text-white rounded">Kirim Penolakan</button>
      </div>
    </form>
  </div>
@endsection
