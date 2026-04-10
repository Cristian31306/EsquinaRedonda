<?php

namespace App\Http\Controllers;

use App\Models\CashShift;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class CashShiftController extends Controller
{
    public function index()
    {
        return Inertia::render('Shifts/Index', [
            'activeShift' => CashShift::with(['user', 'payments'])->where('status', 'open')->first()
        ]);
    }

    public function history()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant || !$tenant->canAccessAuditing()) {
            return redirect()->route('dashboard')->with('error', 'El módulo de Auditoría Histórica es exclusivo para el Plan Profesional. ¡Crezca hoy mismo!');
        }

        // Traer turnos cerrados con paginación
        $shifts = CashShift::where('status', 'closed')
            ->with(['user'])
            ->withSum('payments as total_collected', 'amount')
            ->orderBy('end_time', 'desc')
            ->paginate(15);

        // Traer el turno abierto actualmente (si existe) para el Admin
        $activeShift = CashShift::where('status', 'open')
            ->with(['user'])
            ->withSum('payments as total_collected', 'amount')
            ->first();

        return Inertia::render('Shifts/History', [
            'shifts' => $shifts,
            'activeShift' => $activeShift
        ]);
    }

    public function open(Request $request)
    {
        // Verificar si hay CUALQUIER turno abierto en el sistema
        $existingShift = CashShift::where('status', 'open')->first();
        
        if ($existingShift) {
            $userName = $existingShift->user->name;
            return back()->withErrors(['error' => "Ya hay un turno abierto por {$userName}. Debe cerrarse antes de abrir uno nuevo."]);
        }

        CashShift::create([
            'user_id' => auth()->id(),
            'start_time' => Carbon::now(),
            'opening_cash' => 0,
            'status' => 'open',
        ]);


        return back()->with('success', 'Turno abierto correctamente.');
    }

    public function close(Request $request, \App\Services\SyncService $syncService)
    {
        $request->validate([
            'closing_cash_declared' => 'required|numeric|min:0',
            'shift_id' => 'nullable|exists:cash_shifts,id'
        ]);

        // Si se pasa shift_id y es admin, permitimos cerrar ese turno específico
        if ($request->shift_id && auth()->user()->isAdmin()) {
            $shift = CashShift::find($request->shift_id);
        } else {
            // De lo contrario, cerramos el turno del usuario actual
            $shift = CashShift::where('user_id', auth()->id())->where('status', 'open')->first();
        }

        if (!$shift || $shift->status !== 'open') {
            return back()->withErrors(['error' => 'No hay un turno abierto válido para cerrar.']);
        }

        $shift->update([
            'end_time' => Carbon::now(),
            'closing_cash_declared' => $request->closing_cash_declared,
            'status' => 'closed',
        ]);

        // Sincronización mandatoria al cierre
        $syncResult = $syncService->push();

        // Cargamos relaciones para el reporte
        $shift->load(['user', 'payments.ticket.vehicle']);

        $successMsg = 'Turno cerrado correctamente.';
        if (!$syncResult['success']) {
            $successMsg .= ' (Nota: Sincronización en la nube pendiente por falla de conexión)';
        }

        return back()->with([
            'success' => 'Turno cerrado correctamente.',
            'printShift' => $shift // Pasamos el turno cerrado para que el frontend lo imprima
        ]);
    }

    public function show(CashShift $cashShift)
    {
        $cashShift->load(['user', 'payments.ticket.vehicle']);
        return Inertia::render('Shifts/Show', [
            'shift' => $cashShift
        ]);
    }
}
