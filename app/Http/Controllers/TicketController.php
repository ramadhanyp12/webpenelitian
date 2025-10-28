<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketDocument;
use App\Models\ApprovalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

// Notifications
use App\Notifications\TicketCreatedNotification;
use App\Notifications\HasilUploadedNotification;


class TicketController extends Controller
{
    public function index(): View
{
    $user = Auth::user();

    if ($user->role === 'admin') {
        // Admin: tiket + relasi user & dokumen
        $tickets = Ticket::with(['user', 'suratDocuments', 'lampiranDocuments'])
            ->latest()
            ->get();

        return view('tickets.admin_index', compact('tickets'));
    }

    // User biasa: tiketnya sendiri + relasi dokumen
    $tickets = Ticket::with(['suratDocuments', 'lampiranDocuments'])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    return view('tickets.index', compact('tickets'));
}


    public function create(): View
{
    $user = Auth::user();
    $p    = $user->profile; // bisa null kalau profil belum dibuat

    // nilai awal untuk form (BISA diubah di form dan TIDAK mengubah tabel profiles)
    $prefill = [
        'nama'             => $user->name,
        'nim'              => optional($p)->nim,
        'program_studi'    => optional($p)->prodi,
        'kampus'           => optional($p)->kampus,
        'tahun_ajaran'     => '',   // biarkan kosong atau isi default sendiri
        'judul_penelitian' => '',
        'keterangan'       => '',
        'lokasi_pengadilan'=> '',
    ];

    return view('tickets.create', compact('prefill'));
}

    public function store(Request $request): \Illuminate\Http\RedirectResponse
{
    $data = $request->validate([
        'nama'              => ['required','string','max:255'],
        'nim'               => ['required','string','max:50'],
        'program_studi'     => ['required','string','max:255'],
        'kampus'            => ['required','string','max:255'],
        'tahun_ajaran'      => ['required','string','max:20'],
        'judul_penelitian'  => [
        'required','string','max:255',
        function ($attribute, $value, $fail) {
            // normalisasi: trim + collapse spasi + lower
            $normalized = mb_strtolower(preg_replace('/\s+/', ' ', trim($value)));

            $exists = \App\Models\Ticket::where('user_id', auth()->id())
                ->whereRaw('LOWER(judul_penelitian) = ?', [$normalized])
                ->exists();

            if ($exists) {
                $fail('Judul penelitian ini sudah pernah kamu gunakan. Silakan pakai judul lain.');
            }
        },
    ],
        'keterangan'        => ['nullable','string'],
        'lokasi_pengadilan' => ['nullable','string','max:255'],

        'surat_files'       => ['nullable','array'],
        'surat_files.*'     => ['file','mimes:pdf','max:20480'],
        'lampiran_files'    => ['nullable','array'],
        'lampiran_files.*'  => ['file','mimes:pdf','max:20480'],
    ]);

    $data['user_id']     = auth()->id();
    $data['status']      = 'dikirim';
    $data['approved_by'] = null;
    $data['approved_at'] = null;

    $ticket = \App\Models\Ticket::create($data);

    if ($request->hasFile('surat_files')) {
        foreach ($request->file('surat_files') as $file) {
            $path = $file->store('tickets/surat', 'public');
            $ticket->documents()->create([
                'type'          => 'surat',
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    if ($request->hasFile('lampiran_files')) {
        foreach ($request->file('lampiran_files') as $file) {
            $path = $file->store('tickets/lampiran', 'public');
            $ticket->documents()->create([
                'type'          => 'lampiran',
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }
    $admins = User::where('role', 'admin')->get();
    Notification::send($admins, new TicketCreatedNotification($ticket));

    return redirect()->route('tickets.index')
        ->with('success', 'Ticket berhasil dibuat.');
}


    public function show(Ticket $ticket): View
{
    if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
        abort(403);
    }

    $ticket->load(['user', 'suratDocuments', 'lampiranDocuments']);

    return view('tickets.show', compact('ticket'));
}

    public function edit(Ticket $ticket): View
{
    // Kalau kamu punya pengecekan pemilik/admin:
    if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
        abort(403);
    }

    // Eager-load relasi dokumen
    $ticket->load(['suratDocuments', 'lampiranDocuments']);

    return view('tickets.edit', compact('ticket'));
}

    public function update(Request $request, \App\Models\Ticket $ticket): \Illuminate\Http\RedirectResponse
{
    $this->authorizeTicket($ticket);

    $data = $request->validate([
        'nama'              => ['required','string','max:255'],
        'nim'               => ['required','string','max:50'],
        'program_studi'     => ['required','string','max:255'],
        'kampus'            => ['required','string','max:255'],
        'tahun_ajaran'      => ['required','string','max:20'],
        'judul_penelitian'  => [
        'required','string','max:255',
        function ($attribute, $value, $fail) use ($ticket) {
            $normalized = mb_strtolower(preg_replace('/\s+/', ' ', trim($value)));

            $exists = \App\Models\Ticket::where('user_id', auth()->id())
                ->whereRaw('LOWER(judul_penelitian) = ?', [$normalized])
                ->where('id', '!=', $ticket->id) // abaikan record yang sedang diedit
                ->exists();

            if ($exists) {
                $fail('Judul penelitian ini sudah pernah kamu gunakan. Silakan pakai judul lain.');
            }
        },
    ],
        'keterangan'        => ['nullable','string'],
        'lokasi_pengadilan' => ['nullable','string','max:255'],

        'surat_files'       => ['nullable','array'],
        'surat_files.*'     => ['file','mimes:pdf','max:20480'],
        'lampiran_files'    => ['nullable','array'],
        'lampiran_files.*'  => ['file','mimes:pdf','max:20480'],
    ]);

    unset($data['surat_files'], $data['lampiran_files']);
    $ticket->update($data);

    if ($request->hasFile('surat_files')) {
        foreach ($request->file('surat_files') as $file) {
            $path = $file->store('tickets/surat', 'public');
            $ticket->documents()->create([
                'type'          => 'surat',
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    if ($request->hasFile('lampiran_files')) {
        foreach ($request->file('lampiran_files') as $file) {
            $path = $file->store('tickets/lampiran', 'public');
            $ticket->documents()->create([
                'type'          => 'lampiran',
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    return redirect()->route('tickets.index')
        ->with('success', 'Ticket berhasil diperbarui.');
}



    public function destroy(Ticket $ticket)
{
    // muat relasi dokumen + approval
    $ticket->load(['documents', 'suratDocuments', 'lampiranDocuments', 'approvalDocument']);

    // 1) Hapus file dokumen (surat & lampiran)
    foreach ($ticket->documents as $doc) {
        if ($doc->file_path) {
            Storage::disk('public')->delete($doc->file_path);
        }
        $doc->delete();
    }

    // 2) Hapus hasil PDF (kalau ada, mis. setelah approval)
    if (!empty($ticket->hasil_pdf_path)) {
        Storage::disk('public')->delete($ticket->hasil_pdf_path);
    }

    // 3) Bila ada Approval terkait, hapus file2nya juga
    if ($ticket->approvalDocument) {
        $appr = $ticket->approvalDocument;

        if (!empty($appr->ttd_path))      Storage::disk('public')->delete($appr->ttd_path);
        if (!empty($appr->stempel_path))  Storage::disk('public')->delete($appr->stempel_path);
        if (!empty($appr->generated_pdf_path)) Storage::disk('public')->delete($appr->generated_pdf_path);

        $appr->delete();
    }

    // 4) Hapus tiket
    $ticket->delete();

    return redirect()
        ->route('admin.tickets.index')
        ->with('success', 'Ticket beserta dokumen terkait berhasil dihapus.');
}

// TicketController.php
public function uploadHasil(Request $request, Ticket $ticket)
{
    abort_unless($ticket->user_id === auth()->id(), 403);

    $request->validate([
        'hasil' => ['required','file','mimes:pdf','max:20480'],
    ]);

    if ($ticket->hasil_penelitian_path) {
        Storage::disk('public')->delete($ticket->hasil_penelitian_path);
    }

    $path = $request->file('hasil')->store('tickets/hasil', 'public');

    $ticket->update(['hasil_penelitian_path' => $path]);
    // kirim notifikasi ke semua admin bahwa user sudah upload hasil
$admins = User::where('role', 'admin')->get();
    Notification::send($admins, new HasilUploadedNotification($ticket));

    return back()->with('success', 'Hasil penelitian berhasil diupload. Menunggu verifikasi admin.');
}

public function destroyHasil(Ticket $ticket)
{
    abort_unless($ticket->user_id === auth()->id(), 403);

    if ($ticket->hasil_penelitian_path) {
        Storage::disk('public')->delete($ticket->hasil_penelitian_path);
    }

    $ticket->update(['hasil_penelitian_path' => null]);

    return back()->with('success', 'Hasil penelitian dihapus.');
}

}
