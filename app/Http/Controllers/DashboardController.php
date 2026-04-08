<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Payment;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.tenants.index');
        }

        $today = Carbon::today();
        
        $totalCollectedToday = Payment::whereDate('created_at', $today)->sum('amount');
        
        // Optimización: Solo traer los 50 vehículos más recientes para el Dashboard rápido
        $currentVehicles = Ticket::with(['vehicle:id,plate,type', 'user:id,name'])
            ->select('id', 'vehicle_id', 'user_id', 'entry_time', 'status')
            ->where('status', 'active')
            ->latest('entry_time')
            ->limit(50)
            ->get();

        // Alertas: Traer solo las necesarias
        $alerts = Ticket::with(['vehicle:id,plate', 'user:id,name'])
            ->select('id', 'vehicle_id', 'user_id', 'entry_time', 'status')
            ->where('status', 'active')
            ->where('entry_time', '<', Carbon::now()->subHours(24))
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_today' => (float) $totalCollectedToday,
                'inventory_count' => Ticket::where('status', 'active')->count(),
                'alerts_count' => $alerts->count(),
            ],
            'inventory' => $currentVehicles,
            'alerts' => $alerts,
        ]);
    }
}
