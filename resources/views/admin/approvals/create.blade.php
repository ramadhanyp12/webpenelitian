@extends('layouts.admin')

@section('title','Buat Approval')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Buat Approval untuk: {{ $ticket->user->name }} (Ticket #{{ $ticket->id }})</h1>

  @if ($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.approvals.store', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
    @csrf

    {{-- Prefill dari ticket --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Nama Mahasiswa</label>
        <input name="nama_mahasiswa" class="w-full border rounded px-3 py-2"
               value="{{ old('nama_mahasiswa', $ticket->nama) }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">NIM</label>
        <input name="nim_mahasiswa" class="w-full border rounded px-3 py-2"
               value="{{ old('nim_mahasiswa', $ticket->nim) }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">Program Studi</label>
        <input name="prodi_mahasiswa" class="w-full border rounded px-3 py-2"
               value="{{ old('prodi_mahasiswa', $ticket->program_studi) }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">Judul Penelitian</label>
        <input name="judul_penelitian" class="w-full border rounded px-3 py-2"
               value="{{ old('judul_penelitian', $ticket->judul_penelitian) }}" required>
      </div>
    </div>

    <hr class="my-2">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Nama Penandatangan</label>
        <input name="nama_penandatangan" class="w-full border rounded px-3 py-2" value="{{ old('nama_penandatangan') }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">NIP Penandatangan</label>
        <input name="nip_penandatangan" class="w-full border rounded px-3 py-2" value="{{ old('nip_penandatangan') }}" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">Pangkat/Gol</label>
        <input name="pangkat_gol" class="w-full border rounded px-3 py-2" value="{{ old('pangkat_gol') }}">
      </div>
      <div>
        <label class="block mb-1 font-medium">Jabatan Penandatangan</label>
        <input name="jabatan_penandatangan" class="w-full border rounded px-3 py-2" value="{{ old('jabatan_penandatangan') }}">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Nomor Surat</label>
        <input name="nomor_surat" class="w-full border rounded px-3 py-2" value="{{ old('nomor_surat') }}" required>
      </div>
      @error('nomor_surat')
  <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
@enderror
      <div>
        <label class="block mb-1 font-medium">Tanggal Surat</label>
        <input type="date" name="tanggal_surat" class="w-full border rounded px-3 py-2" value="{{ old('tanggal_surat') }}" required>
      </div>
      <div class="md:col-span-2">
        <label class="block mb-1 font-medium">Tujuan (Kepada Yth.)</label>
        <input name="tujuan" class="w-full border rounded px-3 py-2" value="{{ old('tujuan') }}" required>
      </div>
    </div>


    <div class="pt-2 flex gap-3">
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      <a href="{{ route('admin.approvals.index') }}" class="px-4 py-2 rounded border">Batal</a>
    </div>
  </form>
@endsection
