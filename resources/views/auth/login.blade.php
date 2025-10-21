{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Login - Website izin penelitian</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    /* fallback kalau Tailwind belum termuat */
    body,html{height:100%}
  </style>
</head>
<body class="min-h-screen bg-cover bg-center"
      style="background-image:url('{{ asset('images/pta.png') }}');">

  <div class="min-h-screen flex items-center justify-center bg-black/30 px-4">
    <div class="w-full max-w-md">
      {{-- Kartu form --}}
      <div class="bg-white/95 rounded-2xl shadow-xl p-7">

        {{-- Header/logo --}}
        <div class="flex flex-col items-center mb-6">
          <img src="{{ asset('images/logo.png') }}" class="h-16 w-16 object-contain" alt="Logo">
          <h1 class="mt-3 text-xl font-semibold">Website izin penelitian</h1>
        </div>

        {{-- Notifikasi/Errors --}}
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

        {{-- Form login --}}
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

          {{-- Tidak ada lupa password --}}

          <button type="submit"
                  class="w-full rounded-lg bg-blue-600 text-white py-2.5 hover:bg-blue-700 transition">
            MASUK
          </button>
        </form>

        {{-- Register link --}}
        <div class="text-center mt-4 text-sm">
          Belum punya akun?
          <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Registrasi Akun</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
