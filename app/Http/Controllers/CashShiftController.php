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
            'activeShift' => CashShift::with('payments')->where('user_id', auth()->id())->where('status', 'open')->first()
        ]);
    }

    public function history()
    {
        $shifts = CashShift::with('user', 'payments')->orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Shifts/History', [
            'shifts' => $shifts
        ]);
    }

    public function open(Request $request)
    {
        $existingShift = CashShift::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($existingShift) {
            return back()->withErrors(['error' => 'Ya tienes un turno abierto.']);
        }

        CashShift::create([
            'user_id' => auth()->id(),
            'start_time' => Carbon::now(),
            'opening_cash' => 0,
            'status' => 'open',
        ]);

        return back()->with('success', 'Turno abierto correctamente.');
    }

    public function close(Request $request)
    {
        $request->validate([
            'closing_cash_declared' => 'required|numeric|min:0',
        ]);

        $shift = CashShift::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$shift) {
            return back()->withErrors(['error' => 'No hay un turno abierto para cerrar.']);
        }

        $shift->update([
            'end_time' => Carbon::now(),
            'closing_cash_declared' => $request->closing_cash_declared,
            'status' => 'closed',
        ]);

        return back()->with('success', 'Turno cerrado correctamente.');
    }

    public function show(CashShift $cashShift)
    {
        $cashShift->load(['user', 'payments.ticket.vehicle']);
        return Inertia::render('Shifts/Show', [
            'shift' => $cashShift
        ]);
    }
}
