
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\InventarioController;

// ============================================
// PÁGINA PRINCIPAL
// ============================================

Route::view('/', 'welcome')->name('home');

// ============================================
// RUTAS PROTEGIDAS
// ============================================

Route::middleware(['auth', 'verified'])->group(function () {

    // ========================================
    // DASHBOARD
    // ========================================

    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    // ========================================
    // ADMINISTRACIÓN
    // ========================================

    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {

            // ====================================
            // GESTIÓN DE USUARIOS
            // ====================================

            Route::prefix('usuarios')
                ->name('usuarios.')
                ->controller(UsuarioController::class)
                ->group(function () {

                    // Listado
                    Route::get('/', 'index')
                        ->middleware('permission:usuarios.ver')
                        ->name('index');

                    // Crear
                    Route::get('/create', 'create')
                        ->middleware('permission:usuarios.crear')
                        ->name('create');

                    Route::post('/', 'store')
                        ->middleware('permission:usuarios.crear')
                        ->name('store');

                    // Restaurar
                    Route::patch('/{id}/restaurar', 'restaurar')
                        ->middleware('permission:usuarios.restaurar')
                        ->name('restaurar');

                    // Editar
                    Route::get('/{id}/edit', 'edit')
                        ->middleware('permission:usuarios.editar')
                        ->name('edit');

                    Route::put('/{id}', 'update')
                        ->middleware('permission:usuarios.editar')
                        ->name('update');

                    Route::patch('/{id}', 'update')
                        ->middleware('permission:usuarios.editar');

                    // Eliminar
                    Route::delete('/{id}', 'destroy')
                        ->middleware('permission:usuarios.eliminar')
                        ->name('destroy');

                    // Ver detalles
                    Route::get('/{id}', 'show')
                        ->middleware('permission:usuarios.ver')
                        ->name('show');

                });

            // ====================================
            // GESTIÓN DE ROLES Y PERMISOS
            // ====================================

            Route::prefix('roles')
                ->name('roles.')
                ->controller(RoleController::class)
                ->group(function () {

                    // Listado
                    Route::get('/', 'index')
                        ->middleware('permission:roles.ver')
                        ->name('index');

                    // Operaciones exclusivas SUPER ADMIN
                    Route::middleware('role:SUPER ADMIN')
                        ->group(function () {

                            Route::get('/create', 'create')
                                ->middleware('permission:roles.crear')
                                ->name('create');

                            Route::post('/', 'store')
                                ->middleware('permission:roles.crear')
                                ->name('store');

                            Route::get('/{id}/edit', 'edit')
                                ->middleware('permission:roles.editar')
                                ->name('edit');

                            Route::put('/{id}', 'update')
                                ->middleware('permission:roles.editar')
                                ->name('update');

                            Route::patch('/{id}', 'update')
                                ->middleware('permission:roles.editar');

                            Route::delete('/{id}', 'destroy')
                                ->middleware('permission:roles.eliminar')
                                ->name('destroy');

                        });

                    // Ver detalles
                    Route::get('/{id}', 'show')
                        ->middleware('permission:roles.ver')
                        ->name('show');

                });

            // ====================================
            // GESTIÓN DE CATEGORÍAS
            // ====================================

            Route::prefix('categorias')
                ->name('categorias.')
                ->controller(CategoriaController::class)
                ->group(function () {

                    // Listado
                    Route::get('/', 'index')
                        ->middleware('permission:categorias.ver')
                        ->name('index');

                    // Crear
                    Route::get('/create', 'create')
                        ->middleware('permission:categorias.crear')
                        ->name('create');

                    Route::post('/', 'store')
                        ->middleware('permission:categorias.crear')
                        ->name('store');

                    // Restaurar
                    Route::patch('/{id}/restaurar', 'restaurar')
                        ->middleware('permission:categorias.restaurar')
                        ->name('restaurar');

                    // Editar
                    Route::get('/{id}/edit', 'edit')
                        ->middleware('permission:categorias.editar')
                        ->name('edit');

                    Route::put('/{id}', 'update')
                        ->middleware('permission:categorias.editar')
                        ->name('update');

                    Route::patch('/{id}', 'update')
                        ->middleware('permission:categorias.editar');

                    // Eliminar
                    Route::delete('/{id}', 'destroy')
                        ->middleware('permission:categorias.eliminar')
                        ->name('destroy');

                    // Ver detalles
                    Route::get('/{id}', 'show')
                        ->middleware('permission:categorias.ver')
                        ->name('show');

                });


            // ====================================
            // GESTIÓN DE PRODUCTOS
            // ====================================

            Route::prefix('productos')
                ->name('productos.')
                ->controller(ProductoController::class)
                ->group(function () {

                    // Listado de productos
                    Route::get('/', 'index')
                        ->middleware('permission:productos.ver')
                        ->name('index');

                    // Registrar producto
                    Route::get('/create', 'create')
                        ->middleware('permission:productos.crear')
                        ->name('create');

                    Route::post('/', 'store')
                        ->middleware('permission:productos.crear')
                        ->name('store');

                    // Restaurar producto eliminado
                    Route::patch('/{id}/restaurar', 'restaurar')
                        ->middleware('permission:productos.restaurar')
                        ->name('restaurar');

                    // Editar producto
                    Route::get('/{id}/edit', 'edit')
                        ->middleware('permission:productos.editar')
                        ->name('edit');

                    Route::put('/{id}', 'update')
                        ->middleware('permission:productos.editar')
                        ->name('update');

                    Route::patch('/{id}', 'update')
                        ->middleware('permission:productos.editar');

                    // Eliminar producto
                    Route::delete('/{id}', 'destroy')
                        ->middleware('permission:productos.eliminar')
                        ->name('destroy');

                    // Ver detalles
                    Route::get('/{id}', 'show')
                        ->middleware('permission:productos.ver')
                        ->name('show');

                });

            // ====================================
            // GESTIÓN DE INVENTARIO
            // ====================================

            Route::prefix('inventario')
                ->name('inventario.')
                ->controller(InventarioController::class)
                ->group(function () {

                    // Listado de inventario
                    Route::get('/', 'index')
                        ->middleware('permission:inventario.ver')
                        ->name('index');

                    // Formulario para registrar lote
                    Route::get('/create', 'create')
                        ->middleware('permission:inventario.gestionar')
                        ->name('create');

                    // Guardar lote
                    Route::post('/', 'store')
                        ->middleware('permission:inventario.gestionar')
                        ->name('store');

                    Route::post('/movimientos', 'registrarMovimiento')
                        ->middleware('permission:inventario.gestionar')
                        ->name('movimientos.store');

                    // ========================================
                    // HISTORIAL DE MOVIMIENTOS POR LOTE
                    // ========================================

                    Route::get('/{lote}/historial', 'historial')
                        ->middleware('permission:inventario.ver')
                        ->name('historial');

                });

        });

});

// ============================================
// CONFIGURACIÓN DEL SISTEMA
// ============================================

require __DIR__.'/settings.php';
