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
            ['email' => 'erick@gmail.com'],
            [
                'name' => 'Erick Fernando Morales Gil',
                'password' => Hash::make('12345678'),
            ]
        );

        $user->assignRole('SUPER ADMIN');
    }
}
