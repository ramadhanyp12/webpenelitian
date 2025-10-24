<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketDocument;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index()
    {
        // Admin lihat semua tiket + eager load dokumen dan user
        $tickets = Ticket::with(['user', 'suratDocuments', 'lampiranDocuments'])
            ->latest()
            ->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
{
    $user = auth()->user();
    $p    = $user->profile; // boleh null

    $prefill = [
        'nama'              => $user->name,
        'nim'               => optional($p)->nim,
        'program_studi'     => optional($p)->prodi,
        'kampus'            => optional($p)->kampus,
        'tahun_ajaran'      => '',
        'lokasi_pengadilan' => 'PTA Gorontalo',
    ];

    return view('admin.tickets.create', compact('prefill'));
}

    public function store(Request $request)
{
    $request->validate([
        'nama'              => ['required','string','max:255'],
        'nim'               => ['required','string','max:50'],
        'program_studi'     => ['required','string','max:255'],
        'kampus'            => ['required','string','max:255'],
        'tahun_ajaran'      => ['required','string','max:20'],

        // Unik PER user (admin yg buat dianggap owner tiket)
        'judul_penelitian'  => [
            'required','string','max:255',
            Rule::unique('tickets','judul_penelitian')
                ->where(fn($q) => $q->where('user_id', auth()->id())),
        ],

        'keterangan'        => ['nullable','string'],
        'lokasi_pengadilan' => ['nullable','string','max:255'],

        'surat_files'       => ['nullable','array'],
        'surat_files.*'     => ['file','mimes:pdf','max:20480'],
        'lampiran_files'    => ['nullable','array'],
        'lampiran_files.*'  => ['file','mimes:pdf','max:20480'],
    ]);

    $data = $request->only([
        'nama','nim','program_studi','kampus','tahun_ajaran',
        'judul_penelitian','keterangan','lokasi_pengadilan'
    ]);

    // kolom sistem
    $data['user_id']     = auth()->id();   // kalau nanti mau “atas nama user lain”, tinggal ganti di sini
    $data['status']      = 'dikirim';
    $data['approved_by'] = null;
    $data['approved_at'] = null;

    $ticket = Ticket::create($data);

    // simpan dokumen
    if ($request->hasFile('surat_files')) {
        foreach ($request->file('surat_files') as $file) {
            $path = $file->store('tickets/surat', 'public');
            TicketDocument::create([
                'ticket_id'     => $ticket->id,
                'type'          => 'surat',
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    if ($request->hasFile('lampiran_files')) {
        foreach ($request->file('lampiran_files') as $file) {
            $path = $file->store('tickets/lampiran', 'public');
            TicketDocument::create([
                'ticket_id'     => $ticket->id,
                'type'          => 'lampiran',
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    return redirect()->route('admin.tickets.index')->with('success', 'Ticket berhasil dibuat.');
}


    public function edit(Ticket $ticket)
    {
        $ticket->load(['suratDocuments', 'lampiranDocuments', 'user']);
        return view('admin.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
{
    // 1) Validasi sesuai form admin
    $data = $request->validate([
        'judul_penelitian'  => ['required','string','max:255'],
        'status'            => ['required','in:dikirim,menunggu_persetujuan,disetujui,ditolak,menunggu_hasil,selesai'],

        // multi-file
        'surat_files'       => ['nullable','array'],
        'surat_files.*'     => ['file','mimes:pdf','max:20480'],
        'lampiran_files'    => ['nullable','array'],
        'lampiran_files.*'  => ['file','mimes:pdf','max:20480'],

        // hasil final (single file)
        'hasil_pdf'         => ['nullable','file','mimes:pdf','max:20480'],
    ]);

    // 2) Update field teks
    $ticket->fill([
        'judul_penelitian' => $data['judul_penelitian'],
        'status'           => $data['status'],
    ]);

    // 3) Simpan file hasil final jika ada (menimpa yang lama)
    if ($request->hasFile('hasil_pdf')) {
        $hasilPath = $request->file('hasil_pdf')->store('tickets/hasil', 'public');
        // pastikan kolom 'hasil_pdf_path' sudah ada di $fillable Ticket
        $ticket->hasil_pdf_path = $hasilPath;
    }

    $ticket->save();

    // 4) Tambahkan file Surat baru (opsional)
    if ($request->hasFile('surat_files')) {
        foreach ($request->file('surat_files') as $file) {
            $path = $file->store('tickets/surat', 'public');
            TicketDocument::create([
                'ticket_id'     => $ticket->id,
                'type'          => 'surat',
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    // 5) Tambahkan file Lampiran baru (opsional)
    if ($request->hasFile('lampiran_files')) {
        foreach ($request->file('lampiran_files') as $file) {
            $path = $file->store('tickets/lampiran', 'public');
            TicketDocument::create([
                'ticket_id'     => $ticket->id,
                'type'          => 'lampiran',
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    return redirect()
        ->route('admin.tickets.index')
        ->with('success', 'Ticket berhasil diperbarui.');
}

    public function show(\App\Models\Ticket $ticket)
{
    $ticket->load(['user', 'suratDocuments', 'lampiranDocuments']);
    return view('admin.tickets.show', compact('ticket'));
}

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.tickets.index')->with('success', 'Ticket dihapus.');
    }

    public function destroyDocument(Ticket $ticket, TicketDocument $document): RedirectResponse
{
    // Admin bebas; tetap cek keterkaitan agar aman
    if ($document->ticket_id !== $ticket->id) {
        abort(404);
    }

    Storage::disk('public')->delete($document->file_path);
    $document->delete();

    return back()->with('success', 'Dokumen dihapus.');
}
public function destroyHasil(Ticket $ticket)
{
    if ($ticket->hasil_penelitian_path) {
        Storage::disk('public')->delete($ticket->hasil_penelitian_path);
    }

    $ticket->update(['hasil_penelitian_path' => null]);

    return back()->with('success','Hasil penelitian dihapus.');
}

}
