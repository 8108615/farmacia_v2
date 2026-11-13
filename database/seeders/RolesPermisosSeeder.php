<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // SUPER ADMIN: todos los permisos.
        $superAdmin = Role::findByName('SUPER ADMIN', 'web');

        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // ADMINISTRADOR
        $administrador = Role::findByName('ADMINISTRADOR', 'web');

        $administrador->syncPermissions([
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.restaurar',
            'roles.ver',
            'categorias.ver',
            'categorias.crear',
            'categorias.editar',
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'inventario.ver',
            'inventario.gestionar',
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'compras.ver',
            'compras.crear',
            'ventas.ver',
            'ventas.crear',
            'reportes.ver',
        ]);

        // FARMACÉUTICO
        $farmaceutico = Role::findByName('FARMACÉUTICO', 'web');

        $farmaceutico->syncPermissions([
            'categorias.ver',
            'productos.ver',
            'inventario.ver',
            'ventas.ver',
        ]);

        // CAJERO
        $cajero = Role::findByName('CAJERO', 'web');

        $cajero->syncPermissions([
            'productos.ver',
            'inventario.ver',
            'ventas.ver',
            'ventas.crear',
        ]);

        // ALMACÉN
        $almacen = Role::findByName('ALMACÉN', 'web');

        $almacen->syncPermissions([
            'categorias.ver',
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'inventario.ver',
            'inventario.gestionar',
            'proveedores.ver',
            'compras.ver',
            'compras.crear',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
