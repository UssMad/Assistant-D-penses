<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepensesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecuController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('recus', RecuController::class);
    Route::post('/recus/{recu}/retry', [RecuController::class, 'retry'])->name('recus.retry');

    Route::get('/depenses', [DepensesController::class, 'index'])->name('depenses.index');
    Route::get('/depenses/create', [DepensesController::class, 'create'])->name('depenses.create');
    Route::post('/depenses', [DepensesController::class, 'store'])->name('depenses.store');
    Route::post('/recus/{recu}/depenses', [DepensesController::class, 'store'])->name('recus.depenses.store');

    Route::get('/depenses/summary', [DepensesController::class, 'summary'])->name('depenses.summary');
    Route::get('/depenses/export', [DepensesController::class, 'export'])->name('depenses.export');

    Route::get('/depenses/{depense}', [DepensesController::class, 'show'])->name('depenses.show');
    Route::get('/depenses/{depense}/edit', [DepensesController::class, 'edit'])->name('depenses.edit');
    Route::put('/depenses/{depense}', [DepensesController::class, 'update'])->name('depenses.update');
    Route::delete('/depenses/{depense}', [DepensesController::class, 'destroy'])->name('depenses.destroy');
});

require __DIR__.'/auth.php';
