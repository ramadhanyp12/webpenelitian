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

// === Root langsung ke login ===
Route::redirect('/', '/login');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Semua route yang butuh login
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ticket routes (CRUD otomatis)
    Route::resource('tickets', TicketController::class);

    // Tambah route hapus dokumen (USER)
    Route::delete('/tickets/{ticket}/documents/{document}', [TicketController::class, 'destroyDocument'])
        ->name('tickets.documents.destroy');
});

Route::middleware(['auth', \App\Http\Middleware\OnlyUser::class])->group(function () {
    Route::get('/password', [\App\Http\Controllers\UserPasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [\App\Http\Controllers\UserPasswordController::class, 'update'])->name('password.update');
});

// --- Khusus admin ---
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Tickets (Admin)
        Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);
        Route::delete(
            'tickets/{ticket}/documents/{document}',
            [\App\Http\Controllers\Admin\TicketController::class, 'destroyDocument']
        )->name('tickets.documents.destroy');

        // ======= APPROVALS (URUTAN SPESIFIK → GENERIK) =======
        // List approvals + antrian tiket
        Route::get('approvals', [\App\Http\Controllers\Admin\ApprovalController::class, 'index'])
            ->name('approvals.index');

        // Buat dari tiket tertentu (HARUS di atas {approval})
        Route::get('approvals/create/{ticket}', [\App\Http\Controllers\Admin\ApprovalController::class, 'create'])
            ->name('approvals.create');
        Route::post('approvals/store/{ticket}', [\App\Http\Controllers\Admin\ApprovalController::class, 'store'])
            ->name('approvals.store');

        // Edit & update approval tertentu
        Route::get('approvals/{approval}/edit', [\App\Http\Controllers\Admin\ApprovalController::class, 'edit'])
            ->name('approvals.edit');
        Route::put('approvals/{approval}', [\App\Http\Controllers\Admin\ApprovalController::class, 'update'])
            ->name('approvals.update');

        // Generate PDF
        Route::post('approvals/{approval}/generate-pdf', [\App\Http\Controllers\Admin\ApprovalController::class, 'generatePdf'])
            ->name('approvals.generatePdf');

        // Tolak tiket
        Route::post('approvals/deny/{ticket}', [\App\Http\Controllers\Admin\ApprovalController::class, 'deny'])
            ->name('approvals.deny');

        // LIHAT/SHOW DETAIL APPROVAL (HARUS di bawah route yg spesifik)
        Route::get('approvals/{approval}', [\App\Http\Controllers\Admin\ApprovalController::class, 'show'])
            ->name('approvals.show');

        // LIHAT/UNDUH PDF HASIL GENERATE
        Route::get('approvals/{approval}/pdf', [\App\Http\Controllers\Admin\ApprovalController::class, 'download'])
            ->name('approvals.pdf');

        // Users (Admin)
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
            ->only(['index','show','edit','update','destroy'])
            ->names('users');
    });


require __DIR__.'/auth.php';

