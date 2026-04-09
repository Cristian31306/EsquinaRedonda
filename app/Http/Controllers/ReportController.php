<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Membership;
use App\Exports\ReportExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportController extends Controller
{
    private array $months = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        $year  = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $monthlyIncome = $this->getMonthlyIncome($year);
        $dailyIncome   = $this->getDailyIncome($year, $month);

        $totalTickets      = Payment::whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('amount');
        $totalMemberships  = Membership::whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('amount_paid');

        return Inertia::render('Reports/Index', [
            'filters'        => ['year' => (int) $year, 'month' => (int) $month],
            'monthlyIncome'  => $monthlyIncome,
            'dailyIncome'    => $dailyIncome,
            'summary'        => [
                'tickets'      => (float) $totalTickets,
                'memberships'  => (float) $totalMemberships,
                'total'        => (float) ($totalTickets + $totalMemberships),
            ],
            'availableYears' => $this->getAvailableYears(),
        ]);
    }

    public function exportExcel(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $tenant = auth()->user()->tenant;
        if (!$tenant || !$tenant->canExportReports()) {
            return back()->with('error', 'La exportación a Excel solo está disponible en el Plan Profesional. ¡Sube de plan para desbloquearla!');
        }

        $year  = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        $name  = "Reporte_{$this->months[$month - 1]}_{$year}.xlsx";

        return Excel::download(new ReportExport($year, $month), $name);
    }

    public function exportPdf(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $tenant = auth()->user()->tenant;
        if (!$tenant || !$tenant->canExportReports()) {
            return back()->with('error', 'La exportación a PDF es una función exclusiva del Plan Profesional. Contacta a soporte para activarla.');
        }

        // Aumentar memoria para reportes grandes
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $year  = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $payments     = Payment::with('ticket.vehicle')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'asc')
            ->get();
        $memberships  = Membership::whereYear('created_at', $year)->whereMonth('created_at', $month)->get();

        $totalTickets     = $payments->sum('amount');
        $totalMemberships = $memberships->sum('amount_paid');

        $pdf = PDF::loadView('reports.monthly-pdf', [
            'year'        => $year,
            'month'       => $month,
            'months'      => $this->months,
            'payments'    => $payments,
            'memberships' => $memberships,
            'summary'     => [
                'tickets'      => (float) $totalTickets,
                'memberships'  => (float) $totalMemberships,
                'total'        => (float) ($totalTickets + $totalMemberships),
            ],
        ])->setPaper('a4', 'portrait');

        $name = "Reporte_{$this->months[$month - 1]}_{$year}.pdf";
        return $pdf->download($name);
    }

    private function getMonthlyIncome($year): array
    {
        $payments    = Payment::selectRaw('DATE_FORMAT(created_at, "%m") as month, SUM(amount) as total')->whereYear('created_at', $year)->groupBy('month')->pluck('total', 'month')->all();
        $memberships = Membership::selectRaw('DATE_FORMAT(created_at, "%m") as month, SUM(amount_paid) as total')->whereYear('created_at', $year)->groupBy('month')->pluck('total', 'month')->all();

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $m = str_pad($i, 2, '0', STR_PAD_LEFT);
            $data[] = (float) (($payments[$m] ?? 0) + ($memberships[$m] ?? 0));
        }
        return $data;
    }

    private function getDailyIncome($year, $month): array
    {
        $payments    = Payment::selectRaw('DATE_FORMAT(created_at, "%d") as day, SUM(amount) as total')->whereYear('created_at', $year)->whereMonth('created_at', $month)->groupBy('day')->pluck('total', 'day')->all();
        $memberships = Membership::selectRaw('DATE_FORMAT(created_at, "%d") as day, SUM(amount_paid) as total')->whereYear('created_at', $year)->whereMonth('created_at', $month)->groupBy('day')->pluck('total', 'day')->all();

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $labels = [];
        $values = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $d = str_pad($i, 2, '0', STR_PAD_LEFT);
            $labels[] = $i;
            $values[] = (float) (($payments[$d] ?? 0) + ($memberships[$d] ?? 0));
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getAvailableYears(): array
    {
        $pYears = Payment::selectRaw('DATE_FORMAT(created_at, "%Y") as year')->distinct()->pluck('year');
        $mYears = Membership::selectRaw('DATE_FORMAT(created_at, "%Y") as year')->distinct()->pluck('year');
        return $pYears->merge($mYears)->unique()->sort()->values()->all() ?: [Carbon::now()->year];
    }

}
