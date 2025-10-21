<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserPasswordController extends Controller
{
    public function edit(): View
    {
        // Pastikan view ini sesuai path yang kamu pakai
        return view('profile.password');
    }

    public function update(Request $request): RedirectResponse
    {
        // Pesan khusus
        $messages = [
            'current_password.required'   => 'Masukkan password lama.',
            'current_password.current_password' => 'Password lama salah.',
            'password.required'           => 'Masukkan password baru.',
            'password.min'                => 'Password baru minimal :min karakter.',
            'password.confirmed'          => 'Konfirmasi password tidak sama dengan password baru.',
        ];

        // Validasi
        $request->validate([
            'current_password' => ['required'], // kita cek manual pakai Hash::check di bawah
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], $messages);

        $user = Auth::user();

        // Cek password lama (manual, agar bisa kirim flash session('error') juga)
        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama salah.'])
                ->with('error', 'Password lama salah.') // <- untuk popup alert
                ->withInput();
        }

        // Simpan password baru
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->route('password.edit')
            ->with('status', 'Password berhasil diubah.');
    }
}
