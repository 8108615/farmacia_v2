<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsuarioController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    // Administración
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:SUPER ADMIN|ADMINISTRADOR')
        ->group(function () {

            // Usuarios
            Route::patch(
                'usuarios/{id}/restaurar',
                [UsuarioController::class, 'restaurar']
            )->name('usuarios.restaurar');

            Route::resource('usuarios', UsuarioController::class);
        });
});

require __DIR__.'/settings.php';
