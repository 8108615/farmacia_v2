<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecutar los seeders principales del sistema Farmacia.
     */
    public function run(): void
    {
        $this->call([
            // 1. Crear roles
            RolesSeeder::class,

            // 2. Registrar permisos
            PermisosSeeder::class,

            // 3. Asignar permisos a los roles
            RolesPermisosSeeder::class,

            // 4. Crear usuario SUPER ADMIN
            SuperAdminSeeder::class,
        ]);
    }
}
