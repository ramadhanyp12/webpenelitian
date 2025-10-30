{{-- resources/views/pdf/approval.blade.php --}}
@php
  $jabatanRaw = trim($approval->jabatan_penandatangan ?? '');
  $lower      = mb_strtolower($jabatanRaw, 'UTF-8');

  // map sederhana
  if (str_contains($lower, 'wakil') && str_contains($lower, 'ketua')) {
      $labelTtd = 'Wakil Ketua';
  } elseif (str_contains($lower, 'ketua')) {
      $labelTtd = 'Ketua';
  } elseif (str_contains($lower, 'sekretaris')) {
      $labelTtd = 'Sekretaris';
  } elseif (str_contains($lower, 'panitera')) {
      $labelTtd = 'Panitera';
  } else {
      $labelTtd = $jabatanRaw; // selain itu tampilkan lengkap
  }

  // tembusan: tampil untuk semua kecuali “Ketua” murni
  $tampilkanTembusan = !(str_contains($lower, 'ketua') && !str_contains($lower, 'wakil'));
@endphp
{{-- resources/views/pdf/approval.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Surat Rekomendasi Penelitian</title>
  <style>
  @page { margin: 18mm 20mm 22mm 20mm; }

  body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color:#000; line-height: 1.45; }

  .kop-wrap{ position: fixed; top: -10mm; left: 0; right: 0; text-align: center; }
  .kop-img{ width: 180mm; height: auto; }
  .kop-spacer{ height: 21mm; }

  .mb-0{margin-bottom:0}
  .mt-8{margin-top:8px}
  .mt-12{margin-top:12px}
  .mt-16{margin-top:16px}
  .mt-20{margin-top:20px}
  .mt-28{margin-top:28px}

  table{ width:100%; border-collapse: collapse; }
  .meta td{ padding:2px 0; vertical-align: top; }
  .label{ width:120px; }
  .colon{ width:12px; }

  /* ====== BLOK TANDA TANGAN (KANAN) ====== */
  .sign{
  width: 82mm;          /* sedikit lebih lebar biar leluasa */
  float: right;
  margin-top: 7mm;      /* jarak dari paragraf terakhir */
  position: relative;
  text-align: left;
}
.baris{ display:block; line-height:1.25; }

/* Salam tepat di atas label jabatan (pojok kanan blok tanda tangan) */
.salam-sign{
  position: absolute;
  right: 2mm;           /* jangan terlalu mepet tepi */
  top: -12mm;           /* pas di atas label jabatan */
  font-style: italic;
}

/* Label jabatan beri ruang kosong untuk stempel+ttd di bawahnya */
.jabatan{
  margin-top: 0;
  margin-bottom: 20mm;  /* ruang untuk stempel & coretan ttd */
}

/* Stempel & TTD: lebih kecil dan overlap presisi */
.stempel{
  position: absolute;
  left: 26mm;           /* geser sedikit ke tengah label */
  top:  -4mm;           /* sedikit naik */
  width: 24mm;          /* kecilkan ukuran stempel */
  opacity: .28;
}
.ttd{
  position: absolute;
  left:  8mm;           /* mulai sedikit kiri agar “jatuh” ke tengah stempel */
  top:   -2mm;          /* sejajarkan dengan label jabatan */
  width: 48mm;          /* kecilkan ukuran tanda tangan */
  opacity: .90;
}

/* Tembusan sejajar paragraf kiri, di bawah blok tanda tangan */
.tembusan{
  position: static;
  clear: both;
  margin-top: 10mm;
  margin-left: 0;       /* otomatis sejajar dengan margin konten (20mm halaman) */
  font-size: 12px;
}
  a{ color:#000; text-decoration:none; }
</style>

</head>
<body>

  {{-- KOP --}}
  <div class="kop-wrap">
    @if(!empty($header_img))
      <img class="kop-img" src="{{ $header_img }}" alt="kop">
    @endif
  </div>
  <div class="kop-spacer"></div>

  {{-- baris nomor + tanggal --}}
  <table class="meta">
    <tr>
      <td class="label">Nomor</td><td class="colon">:</td>
      <td>{{ $approval->nomor_surat }}</td>
      <td style="text-align:right;">Gorontalo, {{ $tanggalCetak ?? \Carbon\Carbon::parse($approval->tanggal_surat)->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
      <td class="label">Lampiran</td><td class="colon">:</td><td>-</td><td></td>
    </tr>
    <tr>
      <td class="label">Hal</td><td class="colon">:</td><td><strong>Rekomendasi Penelitian</strong></td><td></td>
    </tr>
  </table>

  {{-- Tujuan --}}
  <div class="mt-16">
    Kepada Yth; <br>
    {!! nl2br(e($approval->tujuan)) !!}<br>
    Di,-<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Tempat</em>
  </div>

  {{-- Salam --}}
  <p class="mt-16"><em>Assalamu’alaikum Wr. Wb.</em></p>

  {{-- Identitas penandatangan --}}
  <p class="mb-0">Saya yang bertanda tangan di bawah ini:</p>
  <table class="meta">
    <tr><td class="label">Nama</td><td class="colon">:</td><td>{{ $approval->nama_penandatangan }}</td></tr>
    <tr><td class="label">NIP</td><td class="colon">:</td><td>{{ $approval->nip_penandatangan ?: '-' }}</td></tr>
    <tr><td class="label">Pangkat/Gol.Ruang</td><td class="colon">:</td><td>{{ $approval->pangkat_gol ?: '-' }}</td></tr>
    <tr><td class="label">Jabatan</td><td class="colon">:</td><td>{{ $approval->jabatan_penandatangan ?: '-' }}</td></tr>
  </table>

  {{-- Identitas mahasiswa --}}
  <p class="mt-16 mb-0">menerangkan bahwa:</p>
  <table class="meta">
    <tr><td class="label">Nama</td><td class="colon">:</td><td>{{ $approval->nama_mahasiswa }}</td></tr>
    <tr><td class="label">NIM</td><td class="colon">:</td><td>{{ $approval->nim_mahasiswa }}</td></tr>
    <tr><td class="label">Program Studi</td><td class="colon">:</td><td>{{ $approval->prodi_mahasiswa }}</td></tr>
  </table>

  {{-- Isi utama --}}
  <p class="mt-12">
    Telah kami setujui melakukan penelitian di Pengadilan Tinggi Agama Gorontalo, untuk penyusunan
    {{ $jenjang ?? 'Tesis' }} dengan judul <strong>“{{ $approval->judul_penelitian }}”</strong>.
  </p>
  <p>Demikian surat rekomendasi ini diberikan untuk dapat dipergunakan sebagaimana mestinya.</p>
  {{-- Salam penutup --}}
  <div style="clear: both;"></div>

<div class="sign">
  <div class="salam-sign"><em>Wassalamu’alaikum Wr. Wb.</em></div>

  <div class="jabatan">
    <strong class="baris">{{ $labelTtd }}</strong>
    <span class="baris">Gorontalo</span>
  </div>

  @if(!empty($stempel_img))
    <img class="stempel" src="{{ $stempel_img }}">
  @endif
  @if(!empty($ttd_img))
    <img class="ttd" src="{{ $ttd_img }}">
  @endif

  <div class="baris" style="font-weight:700; margin-top: 2mm;">
    {{ $approval->nama_penandatangan }}
  </div>
  <!-- NIP dihilangkan sesuai permintaan -->
</div>

  {{-- Tembusan (muncul untuk selain “Ketua” murni) --}}
@if($tampilkanTembusan)
  <div class="tembusan">
    <strong>Tembusan :</strong><br>
    Yth. Ketua Pengadilan Tinggi Agama Gorontalo.
  </div>
@endif


</body>
</html>

