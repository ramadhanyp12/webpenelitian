@extends('layouts.admin')

@section('title', 'Detail Approval')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Detail Approval</h1>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <div class="bg-white shadow rounded p-4 space-y-2 max-w-2xl">
    <div><span class="font-semibold">Ticket ID:</span> #{{ $approval->ticket_id }}</div>
    <div><span class="font-semibold">User:</span> {{ $approval->ticket->user->name ?? '-' }}</div>
    <div><span class="font-semibold">Nama Mahasiswa:</span> {{ $approval->nama_mahasiswa }}</div>
    <div><span class="font-semibold">NIM:</span> {{ $approval->nim_mahasiswa }}</div>
    <div><span class="font-semibold">Prodi:</span> {{ $approval->prodi_mahasiswa }}</div>
    <div><span class="font-semibold">Judul Penelitian:</span> {{ $approval->judul_penelitian }}</div>
    <div><span class="font-semibold">Tujuan:</span> {{ $approval->tujuan }}</div>
    <div><span class="font-semibold">Nomor & Tanggal Surat:</span> {{ $approval->nomor_surat }} — {{ optional($approval->tanggal_surat)->format('d/m/Y') }}</div>

    <div class="border-t pt-3">
      <div class="font-semibold">Penandatangan</div>
      <div>Nama: {{ $approval->nama_penandatangan }}</div>
      <div>NIP: {{ $approval->nip_penandatangan }}</div>
      <div>Pangkat/Gol: {{ $approval->pangkat_gol ?: '-' }}</div>
      <div>Jabatan: {{ $approval->jabatan_penandatangan ?: '-' }}</div>
    </div>

    <div class="border-t pt-3">
      <div class="font-semibold">Berkas</div>
      <div>
        TTD:
        @if($approval->ttd_path)
          <a class="text-blue-600 underline" target="_blank" href="{{ asset('storage/'.$approval->ttd_path) }}">Lihat</a>
        @else - @endif
      </div>
      <div>
        Stempel:
        @if($approval->stempel_path)
          <a class="text-blue-600 underline" target="_blank" href="{{ asset('storage/'.$approval->stempel_path) }}">Lihat</a>
        @else - @endif
      </div>
      <div>
        PDF Hasil:
        @if($approval->generated_pdf_path)
          <a class="text-blue-600 underline" target="_blank" href="{{ asset('storage/'.$approval->generated_pdf_path) }}">Unduh</a>
        @else
          <span class="text-gray-500">Belum digenerate</span>
        @endif
      </div>
    </div>
  </div>

  <div class="mt-6 flex gap-3">
    <a href="{{ route('admin.approvals.edit', $approval->id) }}" class="bg-yellow-600 text-white px-4 py-2 rounded">Edit</a>

    <form action="{{ route('admin.approvals.generatePdf', $approval->id) }}" method="POST">
      @csrf
      <button class="bg-indigo-600 text-white px-4 py-2 rounded" type="submit">Generate PDF</button>
    </form>

    <a href="{{ route('admin.approvals.index') }}" class="border px-4 py-2 rounded">Kembali</a>
  </div>
@endsection
