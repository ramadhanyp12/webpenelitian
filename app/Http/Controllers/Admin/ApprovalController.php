<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\ApprovalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApprovalGeneratedNotification;
use App\Notifications\TicketDeniedNotification;

class ApprovalController extends Controller
{
    // List tiket yang menunggu persetujuan + approvals yang sudah ada
    public function index()
    {
        $waiting = Ticket::with('user')
            ->where('status', 'menunggu_persetujuan')
            ->latest()->get();

        $approvals = ApprovalDocument::with(['ticket.user'])
            ->latest()->get();

        return view('admin.approvals.index', compact('waiting', 'approvals'));
    }

    // Form create approval (prefill dari ticket)
    public function create(Ticket $ticket)
    {
        // kalau sudah ada, arahkan edit
        if ($ticket->approvalDocument) {
            return redirect()
                ->route('admin.approvals.edit', $ticket->approvalDocument->id)
                ->with('success', 'Approval sudah ada, silakan edit.');
        }

        return view('admin.approvals.create', compact('ticket'));
    }

    // Simpan approval baru
    public function store(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'nama_mahasiswa'        => ['required','string','max:255'],
            'nim_mahasiswa'         => ['required','string','max:255'],
            'prodi_mahasiswa'       => ['required','string','max:255'],
            'nama_penandatangan'    => ['required','string','max:255'],
            'nip_penandatangan'     => ['required','string','max:255'],
            'pangkat_gol'           => ['nullable','string','max:255'],
            'jabatan_penandatangan' => ['nullable','string','max:255'],

            // ⬇️ nomor_surat tidak boleh duplikat
            'nomor_surat'           => ['required','string','max:255','unique:approval_documents,nomor_surat'],

            'tanggal_surat'         => ['required','date'],
            'tujuan'                => ['required','string','max:255'],
            'judul_penelitian'      => ['required','string','max:255'],

            // 'ttd'                   => ['nullable','file','mimes:png,jpg,jpeg','max:5120'],
            // 'stempel'               => ['nullable','file','mimes:png,jpg,jpeg','max:5120'],
        ], [
            'nomor_surat.unique'    => 'Nomor surat sudah digunakan. Silakan pakai nomor lain.',
        ]);

        $data['ticket_id'] = $ticket->id;

        // if ($request->hasFile('ttd')) {
        //     $data['ttd_path'] = $request->file('ttd')->store('approvals/ttd', 'public');
        // }
        // if ($request->hasFile('stempel')) {
        //     $data['stempel_path'] = $request->file('stempel')->store('approvals/stempel', 'public');
        // }

        $approval = ApprovalDocument::create($data);

        // update status tiket -> disetujui
        $ticket->update(['status' => 'disetujui', 'alasan_ditolak' => null]);
        return redirect()
            ->route('admin.approvals.edit', $approval->id)
            ->with('success', 'Approval dibuat. Kamu bisa generate PDF.');
    }

    // Edit approval
    public function edit(ApprovalDocument $approval)
    {
        $approval->load('ticket.user');
        return view('admin.approvals.edit', compact('approval'));
    }

    // Update approval
    public function update(Request $request, ApprovalDocument $approval)
    {
        $data = $request->validate([
            'nama_mahasiswa'        => ['required','string','max:255'],
            'nim_mahasiswa'         => ['required','string','max:255'],
            'prodi_mahasiswa'       => ['required','string','max:255'],
            'nama_penandatangan'    => ['required','string','max:255'],
            'nip_penandatangan'     => ['required','string','max:255'],
            'pangkat_gol'           => ['nullable','string','max:255'],
            'jabatan_penandatangan' => ['nullable','string','max:255'],

            // ⬇️ unique tapi abaikan record yang sedang diedit
            'nomor_surat'           => [
                'required','string','max:255',
                Rule::unique('approval_documents', 'nomor_surat')->ignore($approval->id),
            ],

            'tanggal_surat'         => ['required','date'],
            'tujuan'                => ['required','string','max:255'],
            'judul_penelitian'      => ['required','string','max:255'],

            // 'ttd'                   => ['nullable','file','mimes:png,jpg,jpeg','max:5120'],
            // 'stempel'               => ['nullable','file','mimes:png,jpg,jpeg','max:5120'],
        ], [
            'nomor_surat.unique'    => 'Nomor surat sudah digunakan. Silakan pakai nomor lain.',
        ]);

        // if ($request->hasFile('ttd')) {
        //     if ($approval->ttd_path) Storage::disk('public')->delete($approval->ttd_path);
        //     $data['ttd_path'] = $request->file('ttd')->store('approvals/ttd', 'public');
        // }
        // if ($request->hasFile('stempel')) {
        //     if ($approval->stempel_path) Storage::disk('public')->delete($approval->stempel_path);
        //     $data['stempel_path'] = $request->file('stempel')->store('approvals/stempel', 'public');
        // }

        $approval->update($data);

        return back()->with('success', 'Approval diperbarui.');
    }

    // Set tiket ditolak + simpan alasan
    public function deny(Request $request, Ticket $ticket)
{
    $request->validate([
        'alasan_ditolak' => ['required', 'string']
    ]);

    $ticket->update([
        'status' => 'ditolak',
        'alasan_ditolak' => $request->alasan_ditolak,
    ]);
    $ticket->user->notify(new \App\Notifications\TicketDeniedNotification($ticket));
    return back()->with('success', 'Tiket ditolak. Alasan sudah disimpan & akan terlihat oleh user.');
}

    // Placeholder generate PDF
    public function generatePdf(ApprovalDocument $approval)
{
    $approval->load(['ticket.user']);

    // helper untuk embedd gambar kop/heading
    $encode = function (?string $path) {
        if (!$path) return null;
        $full = public_path($path);
        if (!file_exists($full)) return null;
        $ext  = pathinfo($full, PATHINFO_EXTENSION);
        $bin  = @file_get_contents($full);
        return $bin === false ? null : 'data:image/'.$ext.';base64,'.base64_encode($bin);
    };

    $header_img   = $encode('images/headersurat.png');
    $logo_kop     = $encode('images/logokop.png');

    // catatan: tidak kirim $ttd_img / $stempel_img lagi
    $tanggalCetak = \Carbon\Carbon::parse($approval->tanggal_surat)->translatedFormat('d F Y');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.approval', [
                'approval'     => $approval,
                'header_img'   => $header_img,
                'logo_kop'     => $logo_kop,
                'tanggalCetak' => $tanggalCetak,
            ])
            ->setPaper('A4', 'portrait')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    // simpan hanya sebagai arsip internal admin
    $path = 'approvals/generated/approval-'.$approval->id.'.pdf';
    \Storage::disk('public')->put($path, $pdf->output());

    // update kolom di approval (BUKAN ke tiket/user)
    $approval->update(['generated_pdf_path' => $path]);

    // ubah status tiket -> DISETUJUI (sesuai kebutuhan baru)
    if ($approval->ticket) {
        $approval->ticket->update([
            'status' => 'disetujui',
            // jangan menyentuh hasil_pdf_path di tahap ini
        ]);
    }

    // tidak ada notifikasi ke user di tahap generate
    return back()->with('success', 'PDF internal berhasil dibuat. Status tiket diubah ke "disetujui".');
}

public function releaseForm(ApprovalDocument $approval)
{
    $approval->load('ticket.user');

    // Kamu bisa buat view sederhana admin/approvals/release.blade.php
    // berisi input file "signed_pdf"
    return view('admin.approvals.release', compact('approval'));
}

// Terima upload PDF bertanda tangan & rilis ke user
public function releaseSigned(Request $request, ApprovalDocument $approval)
{
    $request->validate([
        'signed_pdf' => ['required', 'file', 'mimes:pdf', 'max:20480'], // 20 MB
    ]);

    // Simpan file PDF yang sudah TTD manual
    $path = $request->file('signed_pdf')->store('approvals/signed', 'public');

    $approval->update([
        'signed_pdf_path'  => $path,
        'released_to_user' => true,
    ]);

    // Sekarang BARU update tiket & rilis ke user
    if ($approval->ticket) {
        $approval->ticket->update([
            'hasil_pdf_path' => $path,           // muncul di sisi user
            'status'         => 'menunggu_hasil' // sesuai skenario baru
        ]);

        // kirim notifikasi ke user pada tahap ini
        $approval->ticket->user->notify(
            new \App\Notifications\ApprovalGeneratedNotification($approval)
        );
    }

    return redirect()
        ->route('admin.approvals.index')
        ->with('success', 'PDF bertanda tangan dirilis ke user. Status tiket menjadi "menunggu_hasil".');
}

public function show(ApprovalDocument $approval)
{
    $approval->load('ticket.user');
    return view('admin.approvals.show', compact('approval'));
}

public function download(ApprovalDocument $approval)
{
    abort_unless($approval->generated_pdf_path, 404, 'PDF belum digenerate.');

    $full = storage_path('app/public/'.$approval->generated_pdf_path);

    if (!file_exists($full)) {
        abort(404, 'File PDF tidak ditemukan di server.');
    }

    return response()->file($full);
    // Atau kalau mau auto-download:
    // return Storage::disk('public')->download($approval->generated_pdf_path);
}

}
