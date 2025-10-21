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

        // Tickets admin (sudah ada)
        Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);
        Route::delete('tickets/{ticket}/documents/{document}', [\App\Http\Controllers\Admin\TicketController::class, 'destroyDocument'])
              ->name('tickets.documents.destroy');

        Route::get('approvals', [AdminApprovalController::class, 'index'])->name('approvals.index');
        Route::get('approvals/create/{ticket}', [AdminApprovalController::class, 'create'])->name('approvals.create');
        Route::post('approvals/store/{ticket}', [AdminApprovalController::class, 'store'])->name('approvals.store');
        Route::get('approvals/{approval}/edit', [AdminApprovalController::class, 'edit'])->name('approvals.edit');
        Route::put('approvals/{approval}', [AdminApprovalController::class, 'update'])->name('approvals.update');
        Route::post('approvals/{approval}/generate-pdf', [AdminApprovalController::class, 'generatePdf'])->name('approvals.generatePdf');
        Route::post('approvals/deny/{ticket}', [AdminApprovalController::class, 'deny'])->name('approvals.deny');
        // routes/web.php (dalam group admin)
Route::get('approvals/{approval}', [\App\Http\Controllers\Admin\ApprovalController::class, 'show'])
    ->name('approvals.show');

    // routes/web.php (di dalam group admin yg sudah ada)
Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
    ->only(['index','show','edit','update', 'destroy'])
    ->names('users');


    });


require __DIR__.'/auth.php';

