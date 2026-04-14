<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Rate;
use App\Models\Payment;
use App\Models\CashShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Calcula el costo total actual de un ticket basado en su tipo de estadía.
     */
    public function calculateCurrentTotal(Ticket $ticket)
    {
        // Membresía o pago de membresía: siempre $0 al salir
        if (in_array($ticket->stay_type, ['membership', 'membership_payment'])) {
            return 0;
        }

        $vehicleType = $ticket->vehicle->type;
        $rates = Rate::where('vehicle_type', $vehicleType)
            ->where('is_active', true)
            ->get()
            ->keyBy('concept');

        // Margen de 2 horas (120 min) para estadías fijas
        $minutes = $ticket->entry_time->diffInMinutes(Carbon::now());
        $isWithinGracePeriod = $minutes < 120;

        // Estadía fija: día completo (Solo si supera las 2 horas)
        if ($ticket->stay_type === 'fullday' && !$isWithinGracePeriod) {
            return $rates['dia']->value ?? 0;
        }

        // Estadía fija: noche (Solo si supera las 2 horas)
        if ($ticket->stay_type === 'overnight' && !$isWithinGracePeriod) {
            return $rates['noche']->value ?? $rates['dia']->value ?? 0;
        }

        // -- Tarifa normal por tiempo --
        // (Esto se ejecuta si es normal o si es fija pero dentro del margen de 2 horas)
        if ($minutes <= 0) return 0;

        $total = 0;
        $days = floor($minutes / 1440);
        $remainingMinutes = $minutes % 1440;

        if ($days > 0 && isset($rates['dia'])) {
            $total += $days * $rates['dia']->value;
        }

        $hourlyRate   = $rates['hora']->value ?? 0;
        $fractionRate = $rates['fraccion']->value ?? 0;
        $dayRate      = $rates['dia']->value ?? PHP_INT_MAX;

        $subTotalRemaining = 0;
        $hours    = floor($remainingMinutes / 60);
        $fracMins = $remainingMinutes % 60;
        $fractions = ceil($fracMins / 15);

        $subTotalRemaining += $hours * $hourlyRate;
        $subTotalRemaining += $fractions * $fractionRate;

        if ($subTotalRemaining > $dayRate) {
            $subTotalRemaining = $dayRate;
        }

        $total += $subTotalRemaining;

        return $total;
    }

    /**
     * Procesa el pago de un ticket y lo vincula al turno activo.
     */
    public function processPayment(Ticket $ticket, $amount, $method)
    {
        return DB::transaction(function () use ($ticket, $amount, $method) {
            // Solo requerir turno y crear pago si hay dinero de por medio
            if ($amount > 0) {
                $activeShift = CashShift::where('user_id', auth()->id())
                    ->where('status', 'open')
                    ->first();

                if (!$activeShift) {
                    throw new \Exception('Debe abrir caja antes de realizar esta operación de cobro.');
                }

                Payment::create([
                    'ticket_id'      => $ticket->id,
                    'cash_shift_id'  => $activeShift->id,
                    'amount'         => $amount,
                    'payment_method' => $method,
                ]);
            }

            // Siempre actualizar el estado del ticket al finalizar el proceso
            $ticket->update([
                'exit_time' => Carbon::now(),
                'status'    => 'completed',
            ]);

            return true;
        });
    }
}
