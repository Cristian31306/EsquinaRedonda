<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Vehicle;
use App\Models\CashShift;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::with('vehicle')
            ->orderByRaw("CASE WHEN end_date >= ? THEN 0 ELSE 1 END, end_date DESC", [now()->toDateString()])
            ->paginate(20);

        $rates = \App\Models\Rate::where('concept', 'mensualidad')
            ->where('is_active', true)
            ->get();

        return Inertia::render('Memberships/Index', [
            'memberships' => $memberships,
            'rates'       => $rates,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate'        => 'required|string|max:10',
            'vehicle_type' => 'required|string',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'amount_paid'  => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:255',
        ]);

        $plate = strtoupper(trim($request->plate));

        // Verificar duplicados de membresía activa
        $hasActive = Membership::where('plate', $plate)
            ->where('end_date', '>=', Carbon::today())
            ->exists();

        if ($hasActive) {
            return back()->withErrors(['plate' => 'Este vehículo ya tiene una mensualidad activa y vigente.']);
        }

        // Obtener o crear el vehículo
        $vehicle = Vehicle::firstOrCreate(
            ['plate' => $plate],
            ['type' => $request->vehicle_type]
        );

        // Obtener el turno activo del operario para registrar el cobro
        $activeShift = CashShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        return DB::transaction(function () use ($request, $vehicle, $activeShift, $plate) {
            // Crear la membresía
            $membership = Membership::create([
                'vehicle_id'   => $vehicle->id,
                'plate'        => $plate,
                'vehicle_type' => $request->vehicle_type,
                'start_date'   => $request->start_date,
                'end_date'     => $request->end_date,
                'amount_paid'  => $request->amount_paid,
                'cash_shift_id' => $activeShift?->id,
                'notes'        => $request->notes,
            ]);

            // Registrar el pago en la caja si hay un turno abierto y hay monto
            if ($activeShift && $request->amount_paid > 0) {
                // Crear un ticket "membership" para ligar el pago
                $ticket = Ticket::create([
                    'vehicle_id' => $vehicle->id,
                    'entry_time' => Carbon::now(),
                    'exit_time'  => Carbon::now(),
                    'status'     => 'completed',
                    'stay_type'  => 'membership_payment',
                    'user_id'    => auth()->id(),
                ]);

                Payment::create([
                    'ticket_id'      => $ticket->id,
                    'cash_shift_id'  => $activeShift->id,
                    'amount'         => $request->amount_paid,
                    'payment_method' => 'efectivo',
                ]);
            }

            return back()->with('success', 'Mensualidad registrada y cobro procesado a caja.');
        });
    }

    public function destroy(Membership $membership)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        $membership->delete();
        return back()->with('success', 'Mensualidad cancelada.');
    }
}
