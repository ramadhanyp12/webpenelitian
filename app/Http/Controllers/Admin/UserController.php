<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(Request $request)
{
    $q = trim($request->input('q', ''));

    $users = \App\Models\User::with('profile')
        ->when($q !== '', function ($qry) use ($q) {
            $qry->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhereHas('profile', function ($p) use ($q) {
                      $p->where('kampus', 'like', "%{$q}%");
                  });
            });
        })
        ->orderByDesc('id')
        ->paginate(10)                  // << paginate
        ->withQueryString();            // << pertahankan q saat pindah halaman

    return view('admin.users.index', compact('users', 'q'));
}

    public function show(User $user)
    {
        $user->load(['profile', 'tickets' => function ($q) {
            $q->latest();
        }]);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('profile');
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', Rule::unique('users','email')->ignore($user->id)],
            // bidang profile (opsional)
            'nim'            => ['nullable','string','max:50'],
            'program_studi'  => ['nullable','string','max:255'],
            'kampus'         => ['nullable','string','max:255'],
            'tahun_ajaran'   => ['nullable','string','max:20'],
        ]);

        // update users
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        // pastikan profile ada lalu update
        $profileData = collect($validated)->only(['nim','program_studi','kampus','tahun_ajaran'])->toArray();
        if (!empty($profileData)) {
            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);
        }

        return redirect()
            ->route('admin.users.edit', $user->id)
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
{
    // 🚨 Cegah hapus user dengan role admin
    if ($user->role === 'admin') {
        return back()->with('error', 'Akun admin tidak boleh dihapus.');
    }

    // 🚨 Opsional: cegah admin hapus akun dirinya sendiri
    if (auth()->id() === $user->id) {
        return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
    }

    // load relasi yang kita butuhkan untuk penghapusan
    $user->load([
        'profile',
        'tickets.documents',           // semua dokumen tiket (surat/lampiran)
        'tickets.approvalDocument',    // dokumen approval (jika ada)
    ]);

    // Hapus semua ticket milik user
    foreach ($user->tickets as $ticket) {
        // 1) Hapus file hasil final tiket (kalau ada)
        if (!empty($ticket->hasil_pdf_path)) {
            Storage::disk('public')->delete($ticket->hasil_pdf_path);
        }

        // 2) Hapus semua file dokumen ticket (surat & lampiran)
        foreach ($ticket->documents as $doc) {
            if (!empty($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();
        }

        // 3) Hapus approval (kalau ada) + file2nya
        if ($ticket->approvalDocument) {
            $appr = $ticket->approvalDocument;
            foreach (['ttd_path','stempel_path','generated_pdf_path'] as $col) {
                if (!empty($appr->{$col})) {
                    Storage::disk('public')->delete($appr->{$col});
                }
            }
            $appr->delete();
        }

        // 4) Hapus record tiket
        $ticket->delete();
    }

    // 5) Hapus profil user (kalau ada)
    if ($user->profile) {
        $user->profile->delete();
    }

    // 6) Terakhir: hapus user
    $user->delete();

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'User dan seluruh data terkait berhasil dihapus.');
}

}
