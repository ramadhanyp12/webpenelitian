<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan form profile.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update informasi profile (users + profiles).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // data terverifikasi dari FormRequest (sudah termasuk field profile)
        $data = $request->validated();

        // --- update tabel users ---
        $user->name  = $data['name'];
        $user->email = $data['email'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // --- update/insert tabel profiles ---
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone'       => $data['phone'] ?? null,
                'kampus'      => $data['kampus'] ?? null,
                'nim'         => $data['nim'] ?? null,
                'prodi'       => $data['prodi'] ?? null,
                'konsentrasi' => $data['konsentrasi'] ?? null,
            ]
        );

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Hapus akun (tetap seperti semula).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
