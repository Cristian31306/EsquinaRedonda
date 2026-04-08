<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear/Actualizar Empresa principal
        $tenant = \App\Models\Tenant::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Esquina Redonda',
                'slug' => 'esquina-redonda',
                'status' => 'active',
                'plan' => 'vitalicio',
            ]
        );

        // 1. Super Administrador (Algorah / Cristian) - ID 1
        \App\Models\User::withoutGlobalScopes()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Cristian Duran',
                'email' => 'durancristian31306@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('Cristian_5732988$'),
                'role' => 'super_admin',
                'is_active' => true,
                'tenant_id' => null,
            ]
        );

        // 2. Administrador Esquina Redonda - ID 2
        \App\Models\User::withoutGlobalScopes()->updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Admin Esquina',
                'email' => 'admin@esquinaredonda.com',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'admin',
                'is_active' => true,
                'tenant_id' => $tenant->id,
            ]
        );

        // 3. Empleado Esquina Redonda
        \App\Models\User::withoutGlobalScopes()->updateOrCreate(
            ['email' => 'juan@esquinaredonda.com'],
            [
                'name' => 'Juan Empleado',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'user',
                'is_active' => true,
                'tenant_id' => $tenant->id,
            ]
        );

        // Limpiar usuarios antiguos si quedaron huérfanos
        \App\Models\User::withoutGlobalScopes()
            ->whereIn('email', ['admin', 'juan'])
            ->delete();

        // Vincular todos los datos huérfanos a la empresa 1
        $tables = [
            'vehicles',
            'rates',
            'cash_shifts',
            'tickets',
            'payments',
            'memberships',
            'settings',
        ];

        foreach ($tables as $table) {
            \DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $tenant->id]);
        }
    }
}
