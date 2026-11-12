<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL')],
            [
                'name' => env('SEED_ADMIN_NAME'),
                'password' => Hash::make(env('SEED_ADMIN_PASSWORD')),
                'tipo_documento' => 'cedula de identidad',
                'numero_documento' => env('SEED_ADMIN_DOCUMENTO'),
                'celular' => env('SEED_ADMIN_CELULAR'),
                'direccion' => env('SEED_ADMIN_DIRECCION'),
                'fecha_nacimiento' => env('SEED_ADMIN_FECHA_NACIMIENTO'),
                'genero' => env('SEED_ADMIN_GENERO'),
                'estado' => 'Activo',
            ]
        );

        $user->assignRole('SUPER ADMIN');
    }
}
