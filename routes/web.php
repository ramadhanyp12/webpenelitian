<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\UserPasswordController;
use App\Http\Middleware\OnlyUser;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\ApprovalController as AdminApprovalController;
use Illuminate\Support\Facades\Mail;

// === Root langsung ke login ===
Route::redirect('/', '/login');

// Dashboard umum
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===================== USER AREA =====================
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tickets (CRUD)
    Route::resource('tickets', TicketController::class);

    // Hapus dokumen ticket (USER)
    Route::delete('tickets/{ticket}/documents/{document}', [TicketController::class, 'destroyDocument'])
        ->name('tickets.documents.destroy');

    // Upload & hapus HASIL PENELITIAN (USER)
    Route::post('tickets/{ticket}/hasil', [TicketController::class, 'uploadHasil'])
        ->name('tickets.hasil.upload');
    Route::delete('tickets/{ticket}/hasil', [TicketController::class, 'destroyHasil'])
        ->name('tickets.hasil.destroy');
});

// Ganti password (khusus role user biasa)
Route::middleware(['auth', OnlyUser::class])->group(function () {
    Route::get('/password', [UserPasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [UserPasswordController::class, 'update'])->name('password.update');
});

// ===================== ADMIN AREA =====================
Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Tickets (ADMIN)
        Route::resource('tickets', AdminTicketController::class);

        // Hapus dokumen ticket (ADMIN)
        Route::delete('tickets/{ticket}/documents/{document}', [AdminTicketController::class, 'destroyDocument'])
            ->name('tickets.documents.destroy');

        // Hapus HASIL PENELITIAN (ADMIN)
        Route::delete('tickets/{ticket}/hasil', [AdminTicketController::class, 'destroyHasil'])
            ->name('tickets.hasil.destroy');
        Route::delete('tickets/{ticket}/surat-izin', [AdminTicketController::class, 'destroySuratIzin'])
    ->name('tickets.suratizin.destroy');

        // ======= APPROVALS =======
        Route::get('approvals', [AdminApprovalController::class, 'index'])->name('approvals.index');

        // Buat dari tiket tertentu
        Route::get('approvals/create/{ticket}', [AdminApprovalController::class, 'create'])->name('approvals.create');
        Route::post('approvals/store/{ticket}', [AdminApprovalController::class, 'store'])->name('approvals.store');

        // Edit/update
        Route::get('approvals/{approval}/edit', [AdminApprovalController::class, 'edit'])->name('approvals.edit');
        Route::put('approvals/{approval}', [AdminApprovalController::class, 'update'])->name('approvals.update');

        // Generate PDF
        Route::post('approvals/{approval}/generate-pdf', [AdminApprovalController::class, 'generatePdf'])
            ->name('approvals.generatePdf');

        /* >>> Tambahan langkah 6 (upload signed, admin only) <<< */
// Form upload PDF yang sudah TTD manual
Route::get('approvals/{approval}/release', [AdminApprovalController::class, 'releaseForm'])
    ->name('approvals.release');
// Submit upload & rilis ke user (status -> menunggu_hasil + notifikasi)
Route::post('approvals/{approval}/release', [AdminApprovalController::class, 'releaseSigned'])
    ->name('approvals.release.store');
/* <<< selesai tambahan >>> */

        // Tolak tiket
        Route::post('approvals/deny/{ticket}', [AdminApprovalController::class, 'deny'])->name('approvals.deny');

        // Detail approval & unduh PDF
        Route::get('approvals/{approval}', [AdminApprovalController::class, 'show'])->name('approvals.show');
        Route::get('approvals/{approval}/pdf', [AdminApprovalController::class, 'download'])->name('approvals.pdf');

        // Users (ADMIN)
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
            ->only(['index','show','edit','update','destroy'])
            ->names('users');
    });

    Route::get('/mail-test', function () {
    try {
        Mail::raw('Test email dari Laravel', function ($m) {
            $m->to('alamatmu@contoh.com')->subject('Mail Test');
        });
        return 'OK. Cek inbox/spam + folder "Terkirim" di akun Gmail pengirim.';
    } catch (\Throwable $e) {
        return 'Gagal kirim: '.$e->getMessage();
    }
});

require __DIR__.'/auth.php';

