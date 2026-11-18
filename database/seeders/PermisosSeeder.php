<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            // ========================================
            // USUARIOS
            // ========================================
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'usuarios.restaurar',

            // ========================================
            // ROLES
            // ========================================
            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',

            // ========================================
            // CATEGORÍAS
            // ========================================
            'categorias.ver',
            'categorias.crear',
            'categorias.editar',
            'categorias.eliminar',
            'categorias.restaurar',

            // ========================================
            // PRODUCTOS
            // ========================================
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'productos.eliminar',
            'productos.restaurar',

            // ========================================
            // INVENTARIO
            // ========================================
            'inventario.ver',
            'inventario.gestionar',
            'inventario.ajustar',

            // ========================================
            // PROVEEDORES
            // ========================================
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'proveedores.eliminar',
            'proveedores.restaurar',

            // ========================================
            // COMPRAS
            // ========================================
            'compras.ver',
            'compras.crear',
            'compras.editar',
            'compras.anular',

            // ========================================
            // VENTAS
            // ========================================
            'ventas.ver',
            'ventas.crear',
            'ventas.editar',
            'ventas.anular',

            // ========================================
            // REPORTES
            // ========================================
            'reportes.ver',
        ];

        // Registrar permisos sin duplicarlos
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        }

        // Actualizar caché de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
