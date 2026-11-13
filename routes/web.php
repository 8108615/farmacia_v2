<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\RoleController;

// ============================================
// PÁGINA PRINCIPAL
// ============================================

Route::view('/', 'welcome')->name('home');

// ============================================
// RUTAS PROTEGIDAS
// ============================================

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    // ============================================
    // ADMINISTRACIÓN
    // ============================================

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:SUPER ADMIN|ADMINISTRADOR')
        ->group(function () {

            // ============================================
            // GESTIÓN DE USUARIOS
            // ============================================

            // Restaurar usuarios eliminados
            Route::patch(
                'usuarios/{id}/restaurar',
                [UsuarioController::class, 'restaurar']
            )->name('usuarios.restaurar');

            // CRUD de usuarios
            Route::resource('usuarios', UsuarioController::class);

            // ============================================
            // GESTIÓN DE ROLES Y PERMISOS
            // ============================================

            // Operaciones exclusivas del SUPER ADMIN
            // Se registran antes de las rutas dinámicas.

            Route::middleware('role:SUPER ADMIN')
                ->group(function () {

                    Route::resource('roles', RoleController::class)
                        ->only([
                            'create',
                            'store',
                            'edit',
                            'update',
                            'destroy',
                        ]);

                });

            // Consultar roles:
            // SUPER ADMIN y ADMINISTRADOR

            Route::resource('roles', RoleController::class)
                ->only([
                    'index',
                    'show',
                ]);

        });

});

// ============================================
// CONFIGURACIÓN DEL SISTEMA
// ============================================

require __DIR__.'/settings.php';
