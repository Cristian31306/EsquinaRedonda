<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Vehicle;
use App\Models\CashShift;
use App\Models\Membership;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class TicketController extends Controller
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function entry()
    {
        $categories = \App\Models\Rate::where('is_active', true)
            ->pluck('vehicle_type')
            ->unique()
            ->values();

        return Inertia::render('Tickets/Entry', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate'       => 'required|string|max:10',
            'type'        => 'required|string',
            'observation' => 'nullable|string',
            'stay_type'   => 'nullable|string|in:overnight,fullday',
        ]);

        $plate = strtoupper(trim($request->plate));

        // Verificar si ya hay un ticket activo para esta placa
        $activeTicket = Ticket::whereHas('vehicle', function($q) use ($plate) {
            $q->where('plate', $plate);
        })->where('status', 'active')->first();

        if ($activeTicket) {
            return back()->withErrors(['plate' => 'Este vehículo ya tiene un ticket activo.']);
        }

        $vehicle = Vehicle::firstOrCreate(
            ['plate' => $plate],
            ['type' => $request->type, 'observation' => $request->observation]
        );

        // Verificar si tiene membresía activa
        $activeMembership = Membership::where('vehicle_id', $vehicle->id)->active()->first();
        $stayType = $activeMembership ? 'membership' : ($request->stay_type ?? null);

        $ticket = Ticket::create([
            'vehicle_id' => $vehicle->id,
            'entry_time' => Carbon::now(),
            'status'     => 'active',
            'stay_type'  => $stayType,
            'user_id'    => auth()->id(),
        ]);

        return redirect()->route('tickets.entry')->with([
            'success'      => 'Vehículo registrado correctamente.',
            'print_ticket' => $ticket->load('vehicle'),
            'membership'   => $activeMembership ? true : false,
        ]);
    }

    public function exit()
    {
        return Inertia::render('Tickets/Exit');
    }

    public function search(Request $request)
    {
        $plate = $request->query('plate');

        $tickets = Ticket::with('vehicle')
            ->whereHas('vehicle', function($q) use ($plate) {
                $q->where('plate', 'like', "%{$plate}%");
            })
            ->where('status', 'active')
            ->get();

        $tickets->each(function($ticket) {
            $ticket->total = $this->billingService->calculateCurrentTotal($ticket);

            $diff = $ticket->entry_time->diff(now());
            $parts = [];
            if ($diff->d > 0) $parts[] = $diff->d . 'd';
            if ($diff->h > 0) $parts[] = $diff->h . 'h';
            if ($diff->i > 0) $parts[] = $diff->i . 'm';
            if (empty($parts)) $parts[] = '0m';

            $ticket->duration_text = implode(' ', $parts);

            // Verificar membresía activa para el badge en salida
            $ticket->has_active_membership = Membership::where('vehicle_id', $ticket->vehicle_id)
                ->active()
                ->exists();
        });

        return response()->json($tickets);
    }

    public function pay(Ticket $ticket, Request $request)
    {
        // Si el vehículo tiene membresía activa, cerrar a $0
        $hasMembership = $ticket->stay_type === 'membership'
            || Membership::where('vehicle_id', $ticket->vehicle_id)->active()->exists();

        if ($hasMembership) {
            $request->merge(['amount' => 0, 'method' => 'membership']);
        } else {
            $request->validate([
                'amount' => 'required|numeric|min:0',
                'method' => 'required|string',
            ]);
        }

        try {
            $this->billingService->processPayment($ticket, $request->amount, $request->method ?? 'efectivo');
            return redirect()->route('tickets.exit')->with('success', 'Pago procesado y ticket cerrado.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
