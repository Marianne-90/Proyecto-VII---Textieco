<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'mobile' => '1111111111',
            'password' => Hash::make('admin123'),
            'utype' => 'ADM',
        ]);

        // Usuario normal
        User::create([
            'name' => 'Usuario Normal',
            'email' => 'user@example.com',
            'mobile' => '2222222222',
            'password' => Hash::make('user123'),
            'utype' => 'USR',
        ]);
    }
}
