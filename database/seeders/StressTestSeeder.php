<?php

namespace Database\Seeders;

use App\Models\CashShift;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StressTestSeeder extends Seeder
{
    public function run(): void
    {
        set_time_limit(0); // This is a heavy seeder, disable timeout
        // Limpiar datos previos
        DB::statement('PRAGMA foreign_keys = OFF;');
        Payment::truncate();
        Ticket::truncate();
        Vehicle::truncate();
        CashShift::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');
        
        $this->command->info('Tablas truncadas. Iniciando Super Auditoría...');

        // 1. Crear 50 usuarios adicionales (operadores y admins)
        $users = User::factory()->count(50)->create();
        $admin = User::where('role', 'admin')->first() ?: User::factory()->create(['role' => 'admin']);

        // 2. Crear 1,000 vehículos base
        $this->command->info('Generando 1,000 vehículos...');
        $vehicles = Vehicle::factory()->count(1000)->create();

        // 3. Simular historia de 1 año (365 días)
        $this->command->info('Generando historia de 1 año (Turnos y Tickets)...');
        
        $startDate = Carbon::now()->subYear();
        
        for ($i = 0; $i < 365; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            
            // 1-2 turnos por día
            $shiftsCount = rand(1, 2);
            for ($s = 0; $s < $shiftsCount; $s++) {
                $user = $users->random();
                
                $shiftStart = $currentDate->copy()->setHour(rand(6, 12));
                $shiftEnd = $shiftStart->copy()->addHours(rand(6, 10));
                
                $shift = CashShift::create([
                    'user_id' => $user->id,
                    'start_time' => $shiftStart,
                    'end_time' => $shiftEnd,
                    'opening_cash' => rand(50000, 100000),
                    'closing_cash_declared' => 0, // Se actualizará luego
                    'status' => 'closed',
                ]);

                // 10-30 tickets por turno
                $ticketsCount = rand(10, 30);
                $totalRevenue = 0;
                
                for ($t = 0; $t < $ticketsCount; $t++) {
                    $vehicle = $vehicles->random();
                    $entryTime = $shiftStart->copy()->addMinutes(rand(0, 480));
                    $exitTime = $entryTime->copy()->addMinutes(rand(30, 240));
                    
                    $ticket = Ticket::create([
                        'vehicle_id' => $vehicle->id,
                        'user_id' => $user->id,
                        'entry_time' => $entryTime,
                        'exit_time' => $exitTime,
                        'status' => 'completed',
                    ]);

                    $amount = rand(2000, 15000);
                    $totalRevenue += $amount;

                    Payment::create([
                        'ticket_id' => $ticket->id,
                        'cash_shift_id' => $shift->id,
                        'amount' => $amount,
                        'payment_method' => rand(0, 1) ? 'cash' : 'transfer',
                    ]);
                }

                $shift->update([
                    'closing_cash_declared' => $shift->opening_cash + $totalRevenue + rand(-5000, 5000), // Simular descuadres ocasionales
                ]);
            }

            if ($i % 50 == 0) {
                $this->command->info("Progreso: día $i de 365 completado...");
            }
        }

        // 4. Crear 300 tickets activos (Inventario actual)
        $this->command->info('Generando 300 vehículos activos en piso...');
        for ($a = 0; $a < 300; $a++) {
            $vehicle = Vehicle::factory()->create(); // Nuevas placas para activos
            Ticket::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => $users->random()->id,
                'entry_time' => Carbon::now()->subMinutes(rand(10, 600)),
                'status' => 'active',
            ]);
        }

        $this->command->info('¡Super Auditoría completada con éxito!');
    }
}
