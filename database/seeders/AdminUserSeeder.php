<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'Administrador',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'juan'],
            [
                'name' => 'Juan Pérez',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
