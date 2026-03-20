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
        $today = Carbon::today();
        
        $totalCollectedToday = Payment::whereDate('created_at', $today)->sum('amount');
        
        $currentVehicles = Ticket::with('vehicle', 'user')
            ->where('status', 'active')
            ->get();

        $alerts = Ticket::with('vehicle', 'user')
            ->where('status', 'active')
            ->where('entry_time', '<', Carbon::now()->subHours(24))
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_today' => (float) $totalCollectedToday,
                'inventory_count' => $currentVehicles->count(),
                'alerts_count' => $alerts->count(),
            ],
            'inventory' => $currentVehicles,
            'alerts' => $alerts,
        ]);
    }
}
