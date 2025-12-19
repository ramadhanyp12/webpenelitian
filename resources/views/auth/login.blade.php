@extends('layouts.guest')

@section('title','Login - Website izin penelitian')

@section('content')
  <div class="w-full max-w-md">
    <div class="bg-white/95 rounded-2xl shadow-xl p-7">

      <div class="flex flex-col items-center mb-6">
        <img src="{{ asset('images/logo.png') }}" class="h-16 w-16 object-contain" alt="Logo">
        <h1 class="mt-3 text-xl font-semibold">Website izin penelitian</h1>
      </div>

      @if (session('status'))
        <div class="mb-3 rounded border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-800">
          {{ session('status') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-3 rounded border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800">
          <ul class="list-disc ml-5">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input type="email" name="email" required autofocus autocomplete="username"
                 value="{{ old('email') }}"
                 class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Kata sandi</label>
          <input type="password" name="password" required autocomplete="current-password"
                 class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-blue-600 text-white py-2.5 hover:bg-blue-700 transition">
          MASUK
        </button>
      </form>

      <div class="text-center mt-4 text-sm">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Registrasi Akun</a>
      </div>
    </div>
  </div>
@endsection
