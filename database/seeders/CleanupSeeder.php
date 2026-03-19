<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('payments')->truncate();
        DB::table('tickets')->truncate();
        DB::table('vehicles')->delete(); // SQLite sometimes objects to truncate on tables with complex FKs
        DB::table('cash_shifts')->truncate();
        DB::table('memberships')->truncate();
        
        Schema::enableForeignKeyConstraints();
        
        $this->command->info('Base de datos limpiada. Usuarios y Tarifas preservados.');
    }
}
