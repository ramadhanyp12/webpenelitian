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
            ->latest()
            ->paginate(10);

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

    // helper gambar kop
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
    $tanggalCetak = \Carbon\Carbon::parse($approval->tanggal_surat)->translatedFormat('d F Y');

    $pdf = Pdf::loadView('pdf.approval', [
                'approval'     => $approval,
                'header_img'   => $header_img,
                'logo_kop'     => $logo_kop,
                'tanggalCetak' => $tanggalCetak,
            ])->setPaper('A4', 'portrait')
              ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    // --- path & nama file standar ---
    $dir      = 'approvals/generated';
    $filename = "approval-{$approval->id}.pdf";
    $path     = "{$dir}/{$filename}";

    // hapus file lama kalau ada (biar tidak ketukar)
    if ($approval->generated_pdf_path && Storage::disk('public')->exists($approval->generated_pdf_path)) {
        Storage::disk('public')->delete($approval->generated_pdf_path);
    }

    // simpan file
    Storage::disk('public')->put($path, $pdf->output());

    // simpan ke DB
    $approval->update(['generated_pdf_path' => $path]);

    // status tiket ke 'disetujui' (sesuai alurmu)
    if ($approval->ticket) {
        $approval->ticket->update(['status' => 'disetujui']);
    }

    return back()->with('success', 'PDF internal berhasil dibuat.');
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

    $dir      = 'approvals/signed';
    $filename = "approval-{$approval->id}-signed.pdf";
    $path     = "{$dir}/{$filename}";

    // hapus signed lama kalau ada
    if ($approval->signed_pdf_path && Storage::disk('public')->exists($approval->signed_pdf_path)) {
        Storage::disk('public')->delete($approval->signed_pdf_path);
    }

    // simpan file baru dengan nama konsisten
    $uploadedPath = $request->file('signed_pdf')->storeAs($dir, $filename, 'public');

    $approval->update([
        'signed_pdf_path'  => $uploadedPath, // = $path
        'released_to_user' => true,
    ]);

    // update tiket agar user bisa akses
    if ($approval->ticket) {
        $approval->ticket->update([
            'hasil_pdf_path' => $uploadedPath,
            'status'         => 'menunggu_hasil',
        ]);

        $approval->ticket->user->notify(
            new \App\Notifications\ApprovalGeneratedNotification($approval)
        );
    }

    return redirect()
        ->route('admin.approvals.index')
        ->with('success', 'PDF bertanda tangan dirilis ke user, Status berubah menjadi menunggu_hasil.');
}


public function show(ApprovalDocument $approval)
{
    $approval->load('ticket.user');
    return view('admin.approvals.show', compact('approval'));
}

public function download(ApprovalDocument $approval)
{
    abort_unless($approval->generated_pdf_path, 404, 'PDF belum digenerate.');

    $full = Storage::disk('public')->path($approval->generated_pdf_path);
    abort_unless(file_exists($full), 404, 'File PDF tidak ditemukan di server.');

    return response()->file($full);
}

}
