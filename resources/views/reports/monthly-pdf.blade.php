<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 1.5cm 2cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        /* HEADER */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #1e1b4b;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header img {
            width: 60px;
            height: 60px;
            margin-right: 16px;
        }
        .header-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #1e1b4b;
        }
        .header-text p {
            margin: 4px 0 0;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #94a3b8;
        }
        .report-meta {
            margin-left: auto;
            text-align: right;
        }
        .report-meta h2 {
            margin: 0;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            color: #1e1b4b;
        }
        .report-meta p {
            margin: 4px 0 0;
            font-size: 9px;
            color: #94a3b8;
            text-transform: uppercase;
        }

        /* SUMMARY CARDS */
        .summary-grid {
            width: 100%;
            margin-bottom: 24px;
        }
        .summary-grid td {
            width: 33%;
            padding: 0 8px;
            vertical-align: top;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
        }
        .summary-card.total {
            background: #1e1b4b;
            color: #fff;
            border-color: #1e1b4b;
        }
        .summary-card p {
            margin: 0 0 8px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
        }
        .summary-card.total p {
            color: #a5b4fc;
        }
        .summary-card h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: #1e1b4b;
        }
        .summary-card.total h3 {
            color: #fff;
        }

        /* TABLES */
        .section-title {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1e1b4b;
            margin: 24px 0 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e2e8f0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table.data-table thead th {
            background: #1e1b4b;
            color: #fff;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 8px 12px;
            text-align: left;
        }
        table.data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        table.data-table tbody td {
            font-size: 9px;
            padding: 7px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        table.data-table tfoot td {
            font-size: 10px;
            font-weight: 900;
            padding: 10px 12px;
            border-top: 2px solid #1e1b4b;
            color: #1e1b4b;
        }
        .badge {
            display: inline-block;
            background: #f1f5f9;
            border-radius: 4px;
            padding: 2px 6px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <!-- CABECERA -->
    <div class="header">
        <img src="{{ public_path('LogoGrande.png') }}" alt="Logo Esquina Redonda">
        <div class="header-text">
            <h1>Esquina Redonda</h1>
            <p>Sistema de Gestión de Parqueaderos</p>
        </div>
        <div class="report-meta">
            <h2>Reporte de Ingresos</h2>
            <p>{{ $months[$month-1] }} {{ $year }}</p>
            <p style="margin-top:6px;">Generado: {{ now()->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <!-- TARJETAS DE RESUMEN -->
    <table class="summary-grid" cellspacing="0" cellpadding="0">
        <tr>
            <td><div class="summary-card"><p>Ingresos por Tickets</p><h3>{{ '$' . number_format($summary['tickets'], 0, ',', '.') }}</h3></div></td>
            <td><div class="summary-card"><p>Ingresos por Mensualidades</p><h3>{{ '$' . number_format($summary['memberships'], 0, ',', '.') }}</h3></div></td>
            <td><div class="summary-card total"><p>Total del Mes</p><h3>{{ '$' . number_format($summary['total'], 0, ',', '.') }}</h3></div></td>
        </tr>
    </table>

    <!-- TABLA DE TICKETS -->
    <div class="section-title">Detalle de Cobros por Ticket</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Placa</th>
                <th>Tipo Vehículo</th>
                <th>Método de Pago</th>
                <th style="text-align:right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $p)
            <tr>
                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                <td><span class="badge">{{ $p->ticket->vehicle->plate ?? 'N/A' }}</span></td>
                <td>{{ ucfirst($p->ticket->vehicle->type ?? 'N/A') }}</td>
                <td>{{ ucfirst($p->payment_method ?? 'Efectivo') }}</td>
                <td style="text-align:right;font-weight:900;">${{ number_format($p->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:20px;">No hay cobros de tickets registrados en este periodo.</td></tr>
            @endforelse
        </tbody>
        @if($payments->count() > 0)
        <tfoot>
            <tr>
                <td colspan="4">SUBTOTAL TICKETS</td>
                <td style="text-align:right">${{ number_format($summary['tickets'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- TABLA DE MENSUALIDADES -->
    <div class="section-title">Detalle de Mensualidades</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Fecha Pago</th>
                <th>Placa</th>
                <th>Tipo</th>
                <th>Periodo Inicio</th>
                <th>Periodo Fin</th>
                <th style="text-align:right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($memberships as $m)
            <tr>
                <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td><span class="badge">{{ $m->plate }}</span></td>
                <td>{{ ucfirst($m->vehicle_type) }}</td>
                <td>{{ $m->start_date->format('d/m/Y') }}</td>
                <td>{{ $m->end_date->format('d/m/Y') }}</td>
                <td style="text-align:right;font-weight:900;">${{ number_format($m->amount_paid, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:20px;">No hay mensualidades registradas en este periodo.</td></tr>
            @endforelse
        </tbody>
        @if($memberships->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5">SUBTOTAL MENSUALIDADES</td>
                <td style="text-align:right">${{ number_format($summary['memberships'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        Esquina Redonda &mdash; Sistema de Gestión de Parqueaderos &mdash; Reporte {{ $months[$month-1] }} {{ $year }}
    </div>
</body>
</html>
