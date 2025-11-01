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
.baris{ display:block; line-height:1.25; }

.signbox {
    float: right;
    width: 70mm;
    margin-top: 8mm;
    text-align: left;
  }

  .signbox .salam {
  font-style: italic;
  margin-bottom: -1mm;   /* samain jaraknya dengan bawah */
}

  .signbox .jabatan {
  font-weight: 700;
  margin-top: 0;          /* biar nempel langsung */
}

  .signbox .kota {
    margin-top: -1mm;    /* Rapatkan “Gorontalo” ke jabatan */
  }

  .signbox .spacer {
    height: 17mm;        /* Ruang untuk tanda tangan manual */
  }

  .signbox .nama {
    font-weight: 700;
    margin-top: 2mm;
  }

  .tembusan {
    clear: both;
    margin-top: 10mm;
    font-size: 12px;
  }
/* Stempel & TTD: kecilkan dan sejajarkan */
.stempel{
  position:absolute;
  left:22mm;            /* kira-kira tengah blok */
  top:-3mm;             /* sedikit naik */
  width:24mm;           /* lebih kecil */
  opacity:.28;
  z-index:0;
}
.ttd{
  position:absolute;
  left:6mm;             /* mulai sedikit kiri, melintas di atas stempel */
  top:-1mm;
  width:50mm;           /* lebih kecil dari sebelumnya */
  opacity:.9;
  z-index:1;
}
  .penandatangan-nama{
  margin-top:3mm;
  font-weight:700;
  text-align:center;    /* atau left kalau kamu mau rata kiri */
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

{{-- ===== Blok tanda tangan (teks saja, tanpa gambar) ===== --}}
<div class="signbox">
  <div class="salam">Wassalamu’alaikum Wr. Wb.</div>
  <div class="jabatan">{{ $labelTtd }}</div>
  <div class="kota">Gorontalo,</div>

  <div class="spacer"></div> <!-- ruang kosong untuk paraf/ttd manual bila diperlukan -->

  <div class="nama">{{ $approval->nama_penandatangan }}</div>
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

