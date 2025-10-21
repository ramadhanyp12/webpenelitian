<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi - Website izin penelitian</title>
  <style>
    :root{
      --card-w: 520px;
      --radius: 14px;
      --shadow: 0 20px 50px rgba(0,0,0,.25);
      --border: 1px solid rgba(0,0,0,.06);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, "Noto Sans", "Helvetica Neue", Arial, "Apple Color Emoji","Segoe UI Emoji";
      color:#111827;
    }

    /* full-screen blurred background (pakai public/pta.png) */
    .bg{
      position:fixed; inset:0;
      background:url('{{ asset('images/pta.png') }}') center/cover no-repeat;
      filter:blur(6px);
      transform:scale(1.05);
    }
    .scrim{
      position:fixed; inset:0;
      background:rgba(17,24,39,.35);
    }

    /* center wrapper */
    .wrap{
      position:relative;
      min-height:100%;
      display:grid;
      place-items:center;
      padding:32px;
    }

    /* the card */
    .card{
      width: min(100%, var(--card-w));
      border-radius: var(--radius);
      background: #fff;
      box-shadow: var(--shadow);
      border: var(--border);
      padding: 28px 28px;
    }

    .logo{
      display:block;
      width:64px; height:64px; margin:0 auto 10px;
      object-fit:contain;
    }
    h1{
      margin:0 0 18px;
      text-align:center;
      font-size:20px;
      font-weight:700;
    }

    label{
      display:block;
      font-size:12px;
      color:#374151;
      margin:12px 0 6px;
      font-weight:600;
    }
    input{
      width:100%;
      border:1px solid #d1d5db;
      border-radius:10px;
      padding:12px 14px;
      font-size:14px;
      outline:none;
      transition: border-color .15s, box-shadow .15s;
      background:#fff;
    }
    input:focus{
      border-color:#2563eb;
      box-shadow:0 0 0 3px rgba(37,99,235,.15);
    }

    .btn{
      width:100%;
      margin-top:14px;
      border:none;
      border-radius:10px;
      padding:12px 16px;
      font-weight:700;
      font-size:14px;
      letter-spacing:.3px;
      color:#fff;
      background:#2563eb;
      cursor:pointer;
      transition: background .15s, transform .02s;
    }
    .btn:active{ transform: translateY(1px); }
    .btn:hover{ background:#1e40af; }

    .muted{
      margin-top:10px;
      text-align:center;
      font-size:14px;
      color:#4b5563;
    }
    .muted a{
      color:#2563eb; text-decoration:none; font-weight:600;
    }
    .muted a:hover{ text-decoration:underline; }

    .errors{
      background:#fef2f2;
      border:1px solid #fecaca;
      color:#b91c1c;
      border-radius:10px;
      padding:10px 12px;
      font-size:14px;
      margin-bottom:12px;
    }
    .errors ul{margin:6px 0 0 18px}
  </style>
</head>
<body>
  <div class="bg" aria-hidden="true"></div>
  <div class="scrim" aria-hidden="true"></div>

  <div class="wrap">
    <div class="card">

      {{-- Logo di public/logo.png --}}
      <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">

      <h1>Website izin penelitian</h1>

      {{-- Error bag --}}
      @if ($errors->any())
        <div class="errors">
          <strong>Periksa kembali isian kamu:</strong>
          <ul>
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">Nama</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username">

        <label for="password">Kata Sandi</label>
        <input id="password" name="password" type="password" required autocomplete="new-password">

        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

        <button class="btn" type="submit">REGISTER</button>

        <p class="muted">
          Sudah punya akun?
          <a href="{{ route('login') }}">Masuk</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
