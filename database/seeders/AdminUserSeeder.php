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
                'email_verified_at' => now(),
            ]
        );
    }
}
