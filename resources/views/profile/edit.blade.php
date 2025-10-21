@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>

    {{-- flash success --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- validation errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" style="max-width: 720px;">
        @csrf
        @method('PATCH')

        {{-- NAMA (users) --}}
        <div>
            <label class="block mb-1 font-medium">Nama</label>
            <input type="text" name="name"
                   value="{{ old('name', $user->name) }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        {{-- EMAIL (users) --}}
        <div>
            <label class="block mb-1 font-medium">Email</label>
            <input type="email" name="email"
                   value="{{ old('email', $user->email) }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <hr class="my-4">

        {{-- PHONE (profiles) --}}
        <div>
            <label class="block mb-1 font-medium">No. HP</label>
            <input type="text" name="phone"
                   value="{{ old('phone', $profile->phone) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- KAMPUS (profiles) --}}
        <div>
            <label class="block mb-1 font-medium">Asal Kampus</label>
            <input type="text" name="kampus"
                   value="{{ old('kampus', $profile->kampus) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- NIM (profiles) --}}
        <div>
            <label class="block mb-1 font-medium">NIM</label>
            <input type="text" name="nim"
                   value="{{ old('nim', $profile->nim) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- PRODI (profiles) --}}
        <div>
            <label class="block mb-1 font-medium">Program Studi</label>
            <input type="text" name="prodi"
                   value="{{ old('prodi', $profile->prodi) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- KONSENTRASI (profiles) --}}
        <div>
            <label class="block mb-1 font-medium">Konsentrasi</label>
            <input type="text" name="konsentrasi"
                   value="{{ old('konsentrasi', $profile->konsentrasi) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Update
        </button>
    </form>
@endsection
