<?php

namespace Database\Seeders;

use App\Models\Rate;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['carro', 'moto', 'pesado'];
        
        foreach ($types as $type) {
            // Fracción 15 min
            Rate::create([
                'vehicle_type' => $type,
                'concept' => 'fraccion',
                'value' => $type === 'moto' ? 1000 : ($type === 'carro' ? 2000 : 5000),
                'is_active' => true,
            ]);

            // Hora
            Rate::create([
                'vehicle_type' => $type,
                'concept' => 'hora',
                'value' => $type === 'moto' ? 3000 : ($type === 'carro' ? 6000 : 15000),
                'is_active' => true,
            ]);

            // Día
            Rate::create([
                'vehicle_type' => $type,
                'concept' => 'dia',
                'value' => $type === 'moto' ? 15000 : ($type === 'carro' ? 30000 : 80000),
                'is_active' => true,
            ]);
            // Noche
            Rate::create([
                'vehicle_type' => $type,
                'concept' => 'noche',
                'value' => $type === 'moto' ? 10000 : ($type === 'carro' ? 20000 : 50000),
                'is_active' => true,
            ]);
        }
    }
}
