<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketActionController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Tickets – access per ticket is enforced by TicketPolicy.
    Route::resource('tickets', TicketController::class);
    Route::controller(TicketActionController::class)->prefix('tickets/{ticket}')->name('tickets.')->group(function () {
        Route::patch('/assign', 'assign')->name('assign');
        Route::patch('/status', 'status')->name('status');
        Route::patch('/priority', 'priority')->name('priority');
        Route::post('/comments', 'comment')->name('comments.store');
    });

    // Administrator area
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
