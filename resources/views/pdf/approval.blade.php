{{-- resources/views/pdf/approval.blade.php --}}
@php
    // label di atas tanda tangan + kontrol tembusan
    $lower = strtolower($approval->jabatan_penandatangan ?? '');
    if (strpos($lower, 'wakil') !== false && strpos($lower, 'ketua') !== false) {
        $labelTtd = 'Wakil Ketua';
    } elseif (strpos($lower, 'ketua') !== false) {
        $labelTtd = 'Ketua';
    } else {
        $labelTtd = $approval->jabatan_penandatangan ?: '';
    }
    $tampilkanTembusan = (strpos($lower, 'ketua') === false); // kalau Ketua, jangan tampilkan tembusan
@endphp
{{-- resources/views/pdf/approval.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Surat Rekomendasi Penelitian</title>
  <style>
    /* Margin halaman. Top kecil agar kop bisa mepet. */
    @page { margin: 18mm 20mm 22mm 20mm; }

    /* Teks dasar */
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color:#000; line-height: 1.45; }

    /* ===== KOP (pakai gambar) ===== */
    .kop-wrap{
      position: fixed;
      /* angkat ke area margin supaya benar-benar mepet atas */
      top: -10mm; left: 0; right: 0;
      text-align: center;
    }
    .kop-img{
      /* hampir selebar A4 – 180mm nyaman untuk Dompdf */
      width: 180mm; height: auto;
    }
    /* spacer agar konten mulai tepat di bawah kop */
    .kop-spacer{ height: 33mm; } /* kalau masih terlalu renggang/rapat, ubah angka ini 31–35mm */

    /* util */
    .mb-0{margin-bottom:0}
    .mt-8{margin-top:8px}
    .mt-12{margin-top:12px}
    .mt-16{margin-top:16px}
    .mt-20{margin-top:20px}
    .mt-28{margin-top:28px}

    table{ width:100%; border-collapse: collapse; }
    .meta td{ padding:2px 0; vertical-align: top; }
    .label{ width:120px; }     /* kolom “Nomor/Lampiran/Hal/Nama/…” */
    .colon{ width:12px; }

    /* blok tanda tangan kanan */
    .sign{
      width: 85mm; float: right; margin-top: 14mm; position: relative; text-align: center;
    }
    .sign .jabatan{ margin-bottom: 40px; } /* jarak label jabatan ke ttd */
    .stempel{ position:absolute; left: 0; top: -8px; width: 58mm; opacity: .35; }
    .ttd{ position:absolute; left: 20mm; top: 6mm; width: 52mm; opacity: .9; }

    /* tautan non-biru */
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

  {{-- Tempat & tanggal --}}
  <p class="mt-12">Diterbitkan di PTA GORONTALO, pada tanggal {{ $tanggalCetak ?? \Carbon\Carbon::parse($approval->tanggal_surat)->translatedFormat('d F Y') }}.</p>

  {{-- ===== Blok tanda tangan kanan ===== --}}
  @php
    $jabatanText = trim($approval->jabatan_penandatangan ?? '');
    $jabatanAtasTtd = $jabatanText !== '' ? $jabatanText : 'Wakil Ketua';

    // jika jabatan mengandung "ketua" (ketua/wakil ketua), tentukan apakah tembusan perlu ditampilkan
    $lowerJabatan = mb_strtolower($jabatanText, 'UTF-8');
    $tampilkanTembusan = !str_contains($lowerJabatan, 'ketua') || str_contains($lowerJabatan, 'wakil');
  @endphp

  <div class="sign">
    <div class="jabatan"><strong>{{ $jabatanAtasTtd }}</strong></div>

    @if(!empty($stempel_img))
      <img class="stempel" src="{{ $stempel_img }}">
    @endif
    @if(!empty($ttd_img))
      <img class="ttd" src="{{ $ttd_img }}">
    @endif

    <div class="mt-28" style="text-decoration: underline; font-weight:bold;">
      {{ $approval->nama_penandatangan }}
    </div>
    <div>NIP. {{ $approval->nip_penandatangan ?: '-' }}</div>
  </div>

  {{-- Salam penutup --}}
  <div style="clear: both;"></div>
  <p class="mt-20"><em>Wassalamu’alaikum Wr. Wb.</em></p>

  {{-- Tembusan (hanya bila yang tanda tangan bukan “Ketua”, atau khusus Wakil Ketua tetap tampil) --}}
  @if($tampilkanTembusan)
    <div class="mt-8">
      <strong>Tembusan :</strong><br>
      Yth. Ketua Pengadilan Tinggi Agama Gorontalo.
    </div>
  @endif

</body>
</html>

