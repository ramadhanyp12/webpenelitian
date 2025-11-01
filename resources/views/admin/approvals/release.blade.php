@extends('layouts.admin')

@section('title','Upload Surat Izin TTD Digital')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Upload Surat Izin yang Sudah Ditandatangani</h1>

  @if ($errors->any())
    <div class="bg-red-100 text-red-800 border border-red-300 p-3 rounded mb-4">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="bg-white shadow rounded p-5 max-w-xl">
    <div class="mb-4">
      <div><span class="font-semibold">User:</span> {{ $approval->ticket->user->name ?? '-' }}</div>
      <div><span class="font-semibold">No. Surat:</span> {{ $approval->nomor_surat }}</div>
      <div><span class="font-semibold">Judul:</span> {{ $approval->judul_penelitian }}</div>
    </div>

    <form action="{{ route('admin.approvals.release.store', $approval->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <div>
        <label class="block mb-1 font-medium">File PDF yang sudah ditandatangani (TTD digital)</label>
        <input type="file" name="signed_pdf" accept="application/pdf" class="w-full border rounded px-3 py-2" required>
        <p class="text-sm text-gray-500 mt-1">Format: PDF, maks 20 MB.</p>
      </div>

      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="released_to_user" value="1" checked>
        <span>Rilis ke user sekarang (status tiket → <em>menunggu_hasil</em> & kirim notifikasi)</span>
      </label>

      <div class="pt-2 flex gap-3">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Upload</button>
        <a href="{{ route('admin.approvals.index') }}" class="px-4 py-2 rounded border">Batal</a>
      </div>
    </form>
  </div>
@endsection
