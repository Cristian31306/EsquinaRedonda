<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Rate;
use App\Models\CashShift;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GenerateTestData extends Command
{
    protected $signature = 'app:generate-test-data {--days=30 : Días hacia atrás}';
    protected $description = 'Puebla la app con datos realistas para los últimos 30 días';

    public function handle()
    {
        $faker = Faker::create('es_CO');
        $days = (int) $this->option('days');
        
        $this->info("🚀 Iniciando simulación de 30 días de operación...");

        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Operario Pro',
                'email' => 'operario@ejemplo.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        $this->ensureRatesExist();

        // 1. Crear flota de vehículos recurrente
        $vehicles = [];
        for ($i = 0; $i < 40; $i++) {
            $type = $faker->randomElement(['carro', 'moto', 'pesado']);
            $plate = $this->generatePlate($type, $faker);
            $vehicles[] = Vehicle::firstOrCreate(['plate' => $plate], ['type' => $type]);
        }

        // 2. Bucle de días
        for ($i = $days; $i >= 0; $i--) {
            $currentDate = Carbon::now()->subDays($i);
            $this->comment("📅 Procesando: " . $currentDate->toDateString());

            // Simulamos 2 turnos por día
            $shifts = [
                ['start' => '07:00:00', 'end' => '15:00:00'],
                ['start' => '15:00:01', 'end' => '22:00:00']
            ];

            foreach ($shifts as $sTime) {
                $start = $currentDate->copy()->setTimeFromTimeString($sTime['start']);
                $end = $currentDate->copy()->setTimeFromTimeString($sTime['end']);

                if ($start->isFuture()) continue;

                $isToday = $currentDate->isToday();
                $isEndFuture = $end->isFuture();
                
                $shift = CashShift::create([
                    'user_id' => $user->id,
                    'start_time' => $start,
                    'end_time' => ($isToday && $isEndFuture) ? null : $end,
                    'opening_cash' => 50000,
                    'status' => ($isToday && $isEndFuture) ? 'open' : 'closed',
                    'created_at' => $start,
                    'updated_at' => ($isToday && $isEndFuture) ? $start : $end,
                ]);

                // Cantidad de tickets según el día (más flujo lun-vie)
                $count = ($currentDate->isWeekend()) ? rand(15, 30) : rand(25, 55);

                for ($t = 0; $t < $count; $t++) {
                    $v = $faker->randomElement($vehicles);
                    $entry = $start->copy()->addMinutes(rand(5, 470));
                    
                    // Lógica de tipo de ticket
                    $randType = rand(1, 100);
                    if ($randType <= 10) { 
                        // Es una mensualidad que ya existía (entra y sale gratis)
                        Ticket::create([
                            'vehicle_id' => $v->id,
                            'entry_time' => $entry,
                            'exit_time' => $entry->copy()->addHours(rand(1, 9)),
                            'status' => 'completed',
                            'stay_type' => 'membership',
                            'user_id' => $user->id,
                            'created_at' => $entry,
                            'updated_at' => $entry,
                        ]);
                    } else {
                        // Ticket normal que genera pago
                        $exit = $entry->copy()->addMinutes(rand(30, 600));
                        if ($exit->gt($end) && $shift->status == 'closed') $exit = $end;

                        $stayType = $faker->randomElement([null, 'overnight', 'fullday']);
                        $ticket = Ticket::create([
                            'vehicle_id' => $v->id,
                            'entry_time' => $entry,
                            'exit_time' => $exit,
                            'status' => 'completed',
                            'stay_type' => $stayType,
                            'user_id' => $user->id,
                            'created_at' => $entry,
                            'updated_at' => $exit,
                        ]);

                        $amount = $this->calculateFakeAmount($ticket);
                        Payment::create([
                            'ticket_id' => $ticket->id,
                            'cash_shift_id' => $shift->id,
                            'amount' => $amount,
                            'payment_method' => $faker->randomElement(['efectivo', 'efectivo', 'efectivo', 'trasnferencia']),
                            'created_at' => $exit, // El pago se registra a la hora de salida
                            'updated_at' => $exit,
                        ]);
                    }
                }

                // Simular venta de mensualidad (Venta de producto)
                if (rand(1, 10) > 8) {
                    $mv = $faker->randomElement($vehicles);
                    $price = ($mv->type == 'carro') ? 160000 : 70000;
                    
                    Membership::create([
                        'vehicle_id' => $mv->id,
                        'plate' => $mv->plate,
                        'vehicle_type' => $mv->type,
                        'start_date' => $currentDate,
                        'end_date' => $currentDate->copy()->addMonth(),
                        'amount_paid' => $price,
                        'cash_shift_id' => $shift->id,
                        'created_at' => $start->copy()->addMinutes(10),
                        'updated_at' => $start->copy()->addMinutes(10),
                    ]);

                    // El sistema requiere un ticket para el pago
                    $tMem = Ticket::create([
                        'vehicle_id' => $mv->id,
                        'entry_time' => $start->copy()->addMinutes(10),
                        'exit_time' => $start->copy()->addMinutes(15),
                        'status' => 'completed',
                        'stay_type' => 'membership_payment',
                        'user_id' => $user->id,
                        'created_at' => $start->copy()->addMinutes(10),
                        'updated_at' => $start->copy()->addMinutes(15),
                    ]);

                    Payment::create([
                        'ticket_id' => $tMem->id,
                        'cash_shift_id' => $shift->id,
                        'amount' => $price,
                        'payment_method' => 'trasnferencia',
                        'created_at' => $start->copy()->addMinutes(15),
                        'updated_at' => $start->copy()->addMinutes(15),
                    ]);
                }

                if ($shift->status == 'closed') {
                    $shift->update([
                        'closing_cash_declared' => $shift->total_collected + $shift->opening_cash
                    ]);
                }
            }
        }

        $this->info("✅ ¡Simulación completada con éxito! Ya puedes revisar tus reportes.");
    }

    private function generatePlate($type, $faker) {
        if ($type == 'moto') return strtoupper($faker->lexify('???')) . $faker->numerify('##') . $faker->lexify('?');
        return strtoupper($faker->lexify('???')) . $faker->numerify('###');
    }

    private function ensureRatesExist() {
        $data = [
            ['carro', 'hora', 5000], ['carro', 'dia', 25000], ['carro', 'noche', 15000], ['carro', 'mensualidad', 180000],
            ['moto', 'hora', 2000], ['moto', 'dia', 10000], ['moto', 'noche', 6000], ['moto', 'mensualidad', 70000],
        ];
        foreach ($data as $r) {
            Rate::firstOrCreate(['vehicle_type' => $r[0], 'concept' => $r[1]], ['value' => $r[2], 'is_active' => true]);
        }
    }

    private function calculateFakeAmount($ticket) {
        if ($ticket->stay_type == 'fullday') return ($ticket->vehicle->type == 'carro') ? 25000 : 10000;
        if ($ticket->stay_type == 'overnight') return ($ticket->vehicle->type == 'carro') ? 15000 : 6000;
        $hours = max(1, $ticket->entry_time->diffInHours($ticket->exit_time));
        return $hours * (($ticket->vehicle->type == 'carro') ? 5000 : 2000);
    }
}
