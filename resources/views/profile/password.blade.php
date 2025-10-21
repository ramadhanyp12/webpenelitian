@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Ubah Password</h1>

    {{-- SUCCESS BOX --}}
    @if (session('status'))
        <div style="background:#e6ffed;border:1px solid #34d399;color:#065f46;padding:.75rem 1rem;margin-bottom:1rem;">
            {{ session('status') }}
        </div>
    @endif

    {{-- ERROR BOX (validasi) --}}
    @if ($errors->any())
        <div style="background:#fee2e2;border:1px solid #ef4444;color:#991b1b;padding:.75rem 1rem;margin-bottom:1rem;">
            <ul style="margin-left:1rem;list-style:disc;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4" style="max-width:520px">
        @csrf
        @method('PUT')

        <div>
            <label class="block mb-1 font-medium">Password Lama</label>
            <input type="password" name="current_password"
                   class="w-full border rounded px-3 py-2"
                   required>
            @error('current_password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Password Baru</label>
            <input type="password" name="password"
                   class="w-full border rounded px-3 py-2"
                   required>
            @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation"
                   class="w-full border rounded px-3 py-2"
                   required>
            @error('password_confirmation')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Ubah Password
        </button>
    </form>

    {{-- POPUP ALERT: untuk error validasi --}}
    @if ($errors->any())
        <script>
            alert(`Error:\n{!! implode("\n", $errors->all()) !!}`);
        </script>
    @endif

    {{-- POPUP ALERT: untuk flash error eksplisit (mis. password lama salah) --}}
    @if (session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif

    {{-- POPUP ALERT: untuk sukses --}}
    @if (session('status'))
        <script>
            alert("{{ session('status') }}");
        </script>
    @endif
@endsection
