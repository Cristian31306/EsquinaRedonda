<?php

namespace Database\Seeders;

use App\Models\CashShift;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Rate;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarchDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        $users = User::all();
        $rates = Rate::all();

        if ($users->isEmpty() || $rates->isEmpty()) {
            $this->command->error('Necesitas al menos un usuario y una tarifa en la base de datos.');
            return;
        }

        $plates = [
            'ABC123', 'DEF456', 'GHI789', 'JKL001', 'MNO234',
            'PQR567', 'STU890', 'VWX111', 'YZA222', 'BCD333',
            'EFG444', 'HIJ555', 'KLM666', 'NOP777', 'QRS888',
            'TUV999', 'WXY000', 'ZAB121', 'CDE232', 'FGH343',
            'IJK454', 'LMN565', 'OPQ676', 'RST787', 'UVW898',
        ];

        $vehicleTypes = ['carro', 'moto', 'carro', 'carro', 'moto'];

        $year  = 2026;
        $month = 3;

        $this->command->info('Creando datos de demo para Marzo 2026...');

        // Crear 15 turnos de caja distribuidos en el mes
        $shifts = [];
        $daysWithShifts = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];

        foreach ($daysWithShifts as $day) {
            $user       = $users->random();
            $openTime   = Carbon::create($year, $month, $day, rand(7, 9), rand(0, 59), 0);
            $closeTime  = $openTime->copy()->addHours(rand(8, 12))->addMinutes(rand(0, 59));

            $shift = CashShift::create([
                'user_id'                 => $user->id,
                'start_time'             => $openTime,
                'end_time'               => $closeTime,
                'status'                 => 'closed',
                'opening_cash'           => rand(50000, 100000),
                'closing_cash_declared'  => null,
            ]);

            $shifts[$day] = $shift;
        }

        $this->command->info('Turnos creados: ' . count($shifts));

        // Crear ~90 tickets (entradas/salidas) con pago
        $ticketCount = 0;
        for ($i = 0; $i < 90; $i++) {
            $day        = array_rand($shifts);
            $shift      = $shifts[$day];
            $plate      = $plates[array_rand($plates)];
            $vtype      = $vehicleTypes[array_rand($vehicleTypes)];
            $rate       = $rates->where('vehicle_type', $vtype)->first() ?? $rates->first();

            // Buscar o crear vehículo
            $vehicle = Vehicle::firstOrCreate(
                ['plate' => $plate],
                ['type'  => $vtype]
            );

            $entryTime = Carbon::create($year, $month, $day, rand(8, 17), rand(0, 59), 0);
            $exitTime  = $entryTime->copy()->addMinutes(rand(30, 240));
            $amount    = $rate->amount ?? rand(2000, 8000);

            $ticket = Ticket::create([
                'vehicle_id'  => $vehicle->id,
                'user_id'     => $shift->user_id,
                'entry_time'  => $entryTime,
                'exit_time'   => $exitTime,
                'status'      => 'completed',
                'stay_type'   => null,
            ]);

            Payment::create([
                'ticket_id'      => $ticket->id,
                'cash_shift_id'  => $shift->id,
                'amount'         => $amount,
                'payment_method' => rand(0, 1) ? 'efectivo' : 'transferencia',
                'created_at'     => $exitTime,
                'updated_at'     => $exitTime,
            ]);

            $ticketCount++;
        }

        $this->command->info("Tickets y pagos creados: {$ticketCount}");

        // Crear ~10 mensualidades
        $membershipPlates = ['MEN001', 'MEN002', 'MEN003', 'MEN004', 'MEN005', 'MEN006', 'MEN007', 'MEN008', 'MEN009', 'MEN010'];
        $msCount = 0;
        foreach ($membershipPlates as $plate) {
            $day   = rand(1, 15);
            $shift = $shifts[$day] ?? $shifts[array_rand($shifts)];
            $vtype = $vehicleTypes[array_rand($vehicleTypes)];

            $vehicle = Vehicle::firstOrCreate(
                ['plate' => $plate],
                ['type'  => $vtype]
            );

            $createdAt = Carbon::create($year, $month, $day, rand(9, 16), rand(0, 59), 0);

            Membership::create([
                'vehicle_id'    => $vehicle->id,
                'plate'         => $plate,
                'vehicle_type'  => $vtype,
                'start_date'    => $createdAt->copy()->startOfDay(),
                'end_date'      => $createdAt->copy()->addMonth()->startOfDay(),
                'amount_paid'   => rand(50000, 120000),
                'cash_shift_id' => $shift->id,
                'notes'         => 'Mensualidad de demo',
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);

            $msCount++;
        }

        $this->command->info("Mensualidades creadas: {$msCount}");

        DB::statement('PRAGMA foreign_keys = ON');
        $this->command->info('¡Demo de Marzo completado! Total: ' . ($ticketCount + $msCount) . ' registros.');
    }
}
