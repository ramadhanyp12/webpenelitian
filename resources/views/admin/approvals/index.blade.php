@extends('layouts.admin')

@section('title','Daftar Approvals')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Approvals</h1>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 border border-green-300 p-3 rounded mb-4">{{ session('success') }}</div>
  @endif

  {{-- Tiket menunggu persetujuan --}}
  <div class="mb-8">
    <h2 class="font-semibold text-lg mb-2">Menunggu Persetujuan</h2>
    <div class="bg-white shadow rounded overflow-x-auto">
      <table class="min-w-full border">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border">#</th>
            <th class="px-4 py-2 border">User</th>
            <th class="px-4 py-2 border">Judul</th>
            <th class="px-4 py-2 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($waiting as $t)
            <tr>
              <td class="px-4 py-2 border align-top">{{ $loop->iteration }}</td>
              <td class="px-4 py-2 border align-top">{{ $t->user->name }}</td>
              <td class="px-4 py-2 border align-top">{{ $t->judul_penelitian }}</td>
              <td class="px-4 py-2 border">
                <div class="flex flex-wrap items-center gap-2">
                  {{-- Isi data persetujuan (buat ApprovalDocument) --}}
                  <a href="{{ route('admin.approvals.create', $t->id) }}"
                     class="px-3 py-1 bg-blue-600 text-white rounded">Isi Data Persetujuan</a>

                  {{-- Tolak cepat (alasan default) --}}
                  <form class="inline" action="{{ route('admin.approvals.deny', $t->id) }}" method="POST"
                        onsubmit="return confirm('Tolak tiket ini dengan alasan default?');">
                    @csrf
                    <input type="hidden" name="alasan_ditolak" value="Data belum lengkap.">
                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded">Tolak cepat</button>
                  </form>

                  {{-- Toggle form alasan (tanpa JS framework) --}}
                  <details class="w-full mt-2">
                    <summary class="cursor-pointer select-none text-red-700 hover:underline">
                      Tolak dengan alasan sendiri
                    </summary>
                    <form class="mt-2" action="{{ route('admin.approvals.deny', $t->id) }}" method="POST">
                      @csrf
                      <textarea name="alasan_ditolak" rows="2" class="w-full border rounded px-3 py-2"
                                placeholder="Tulis alasan penolakan" required></textarea>
                      <div class="mt-2">
                        <button class="px-3 py-1 bg-red-700 text-white rounded">Kirim Penolakan</button>
                      </div>
                    </form>
                  </details>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Tidak ada</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Approval yang sudah dibuat --}}
  <div>
    <h2 class="font-semibold text-lg mb-2">Approval Tersimpan</h2>
    <div class="bg-white shadow rounded overflow-x-auto">
      <table class="min-w-full border">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border">#</th>
            <th class="px-4 py-2 border">User</th>
            <th class="px-4 py-2 border">No. Surat</th>
            <th class="px-4 py-2 border">Judul</th>
            <th class="px-4 py-2 border">PDF</th>
            <th class="px-4 py-2 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($approvals as $a)
            <tr>
              <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
              <td class="px-4 py-2 border">{{ $a->ticket->user->name ?? '-' }}</td>
              <td class="px-4 py-2 border">{{ $a->nomor_surat }}</td>
              <td class="px-4 py-2 border">{{ $a->judul_penelitian }}</td>
              <td class="px-4 py-2 border">
                @if($a->generated_pdf_path)
                  <a href="{{ asset('storage/'.$a->generated_pdf_path) }}" target="_blank" class="text-blue-600 underline">Lihat PDF</a>
                @else
                  <span class="text-gray-500">-</span>
                @endif
              </td>
              <td class="px-4 py-2 border">
                <div class="flex flex-wrap items-center gap-2">
                  <a href="{{ route('admin.approvals.edit', $a->id) }}" class="text-yellow-600">Edit</a>

                  <form class="inline" action="{{ route('admin.approvals.generatePdf', $a->id) }}" method="POST">
                    @csrf
                    <button class="px-3 py-1 bg-indigo-600 text-white rounded">Generate PDF</button>
                  </form>

                  {{-- Tombol upload signed PDF (hanya muncul kalau sudah ada generated_pdf_path) --}}
@if($a->generated_pdf_path)
  <a href="{{ route('admin.approvals.release', $a->id) }}"
     class="px-3 py-1 bg-green-600 text-white rounded">
     Upload Signed
  </a>
@endif

                  {{-- (opsional) Tolak dari sini juga --}}
                  <details class="w-full">
                    <summary class="cursor-pointer select-none text-red-700 hover:underline">
                      Tolak tiket ini (ubah status + alasan)
                    </summary>
                    <form class="mt-2" action="{{ route('admin.approvals.deny', $a->ticket_id) }}" method="POST">
                      @csrf
                      <textarea name="alasan_ditolak" rows="2" class="w-full border rounded px-3 py-2"
                                placeholder="Tulis alasan penolakan" required></textarea>
                      <div class="mt-2">
                        <button class="px-3 py-1 bg-red-700 text-white rounded">Kirim Penolakan</button>
                      </div>
                    </form>
                  </details>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-4 py-3 text-center text-gray-500">Belum ada approval</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
