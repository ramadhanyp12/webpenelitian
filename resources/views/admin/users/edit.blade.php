@extends('layouts.admin')

@section('title','Edit User')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Edit User</h1>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 border border-green-300 p-3 rounded mb-4">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-5 max-w-2xl">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block mb-1 font-medium">NIM</label>
        <input type="text" name="nim" value="{{ old('nim', optional($user->profile)->nim) }}" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Program Studi</label>
        <input type="text" name="program_studi" value="{{ old('program_studi', optional($user->profile)->program_studi) }}" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Asal Kampus</label>
        <input type="text" name="kampus" value="{{ old('kampus', optional($user->profile)->kampus) }}" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', optional($user->profile)->tahun_ajaran) }}" class="w-full border rounded px-3 py-2">
      </div>
    </div>

    <div class="pt-2 flex gap-3">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border">Batal</a>
    </div>
  </form>
@endsection
