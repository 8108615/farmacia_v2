<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermisosSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            // Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'usuarios.restaurar',

            // Roles
            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',

            // Categorías
            'categorias.ver',
            'categorias.crear',
            'categorias.editar',
            'categorias.eliminar',
            'categorias.restaurar',

            // Productos
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'productos.eliminar',
            'productos.restaurar',

            // Inventario
            'inventario.ver',
            'inventario.gestionar',

            // Proveedores
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'proveedores.eliminar',

            // Compras
            'compras.ver',
            'compras.crear',

            // Ventas
            'ventas.ver',
            'ventas.crear',

            // Reportes
            'reportes.ver',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
