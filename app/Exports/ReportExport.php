<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\Membership;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Drawing;

class ReportExport implements WithEvents, ShouldAutoSize
{
    private array $months = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    public function __construct(
        protected int $year,
        protected int $month,
    ) {}

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setTitle('Reporte Mensual');

                // ── Colores corporativos
                $navy   = '1e1b4b';
                $light  = 'f8fafc';
                $border = 'e2e8f0';
                $white  = 'FFFFFF';
                $green  = '059669';
                $text   = '334155';

                // ========================================
                // BLOQUE 1: LOGO + ENCABEZADO
                // ========================================
                $sheet->mergeCells('A1:G4');
                $sheet->getStyle('A1:G4')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $navy]],
                ]);

                // Intentar insertar logo
                $logoPath = public_path('LogoGrande.png');
                if (file_exists($logoPath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(60);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(8);
                    $drawing->setWorksheet($sheet);
                }

                // Título en el encabezado
                $sheet->getCell('C1')->setValue('PARKIAPP');
                $sheet->getCell('C2')->setValue('Reporte de Ingresos — ' . $this->months[$this->month - 1] . ' ' . $this->year);
                $sheet->getCell('C3')->setValue('Generado: ' . now()->format('Y-m-d H:i'));

                $sheet->getStyle('C1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => $white], 'name' => 'Calibri'],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('C2:C3')->applyFromArray([
                    'font' => ['size' => 9, 'color' => ['rgb' => 'a5b4fc']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // ========================================
                // BLOQUE 2: TARJETAS DE RESUMEN (fila 6)
                // ========================================
                $payments     = Payment::with('ticket.vehicle')->whereYear('created_at', $this->year)->whereMonth('created_at', $this->month)->get();
                $memberships  = Membership::whereYear('created_at', $this->year)->whereMonth('created_at', $this->month)->get();
                $totalTickets     = $payments->sum('amount');
                $totalMemberships = $memberships->sum('amount_paid');
                $grandTotal       = $totalTickets + $totalMemberships;

                $this->summaryCard($sheet, 6, 'A', 'B', 'Ingresos por Tickets', $totalTickets, $navy, $white, $light);
                $this->summaryCard($sheet, 6, 'D', 'E', 'Ingresos por Mensualidades', $totalMemberships, $navy, $white, $light);
                $this->summaryCard($sheet, 6, 'F', 'G', 'TOTAL DEL MES', $grandTotal, $white, $navy, $navy);

                // ========================================
                // BLOQUE 3: TABLA DE TICKETS (desde fila 10)
                // ========================================
                $ticketRow = 10;
                $sheet->mergeCells("A{$ticketRow}:G{$ticketRow}");
                $sheet->getCell("A{$ticketRow}")->setValue('DETALLE DE COBROS POR TICKET');
                $sheet->getStyle("A{$ticketRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => $navy]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $light]],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $navy]]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                ]);

                $ticketRow++;
                $headers = ['Fecha', 'Placa', 'Tipo Vehículo', 'Método de Pago', 'Monto ($)'];
                $cols    = ['A', 'B', 'C', 'D', 'G'];
                foreach ($headers as $i => $h) {
                    $sheet->getCell("{$cols[$i]}{$ticketRow}")->setValue($h);
                }
                $sheet->getStyle("A{$ticketRow}:G{$ticketRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $white], 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $navy]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $ticketRow++;
                $startDataRow = $ticketRow;
                foreach ($payments as $i => $p) {
                    $sheet->getCell("A{$ticketRow}")->setValue($p->created_at->format('d/m/Y H:i'));
                    $sheet->getCell("B{$ticketRow}")->setValue($p->ticket->vehicle->plate ?? 'N/A');
                    $sheet->getCell("C{$ticketRow}")->setValue(ucfirst($p->ticket->vehicle->type ?? 'N/A'));
                    $sheet->getCell("D{$ticketRow}")->setValue(ucfirst($p->payment_method ?? 'Efectivo'));
                    $sheet->getCell("G{$ticketRow}")->setValue((float) $p->amount);
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$ticketRow}:G{$ticketRow}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $light]],
                        ]);
                    }
                    $sheet->getStyle("A{$ticketRow}:G{$ticketRow}")->getFont()->setSize(9)->getColor()->setRGB($text);
                    $ticketRow++;
                }

                if ($payments->isEmpty()) {
                    $sheet->mergeCells("A{$ticketRow}:G{$ticketRow}");
                    $sheet->getCell("A{$ticketRow}")->setValue('No hay cobros de tickets en este período.');
                    $sheet->getStyle("A{$ticketRow}")->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('94a3b8');
                    $ticketRow++;
                }

                // Subtotal tickets
                $sheet->mergeCells("A{$ticketRow}:F{$ticketRow}");
                $sheet->getCell("A{$ticketRow}")->setValue('SUBTOTAL TICKETS');
                $sheet->getCell("G{$ticketRow}")->setValue((float) $totalTickets);
                $sheet->getStyle("A{$ticketRow}:G{$ticketRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $navy]],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $navy]]],
                ]);
                $sheet->getStyle("G{$ticketRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $ticketRow += 2;

                // ========================================
                // BLOQUE 4: TABLA DE MENSUALIDADES
                // ========================================
                $msRow = $ticketRow;
                $sheet->mergeCells("A{$msRow}:G{$msRow}");
                $sheet->getCell("A{$msRow}")->setValue('DETALLE DE MENSUALIDADES');
                $sheet->getStyle("A{$msRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => $navy]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $light]],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $navy]]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                ]);

                $msRow++;
                $msHeaders = ['Fecha Pago', 'Placa', 'Tipo', 'Inicio Período', 'Fin Período', 'Monto ($)'];
                $msCols    = ['A', 'B', 'C', 'D', 'E', 'G'];
                foreach ($msHeaders as $i => $h) {
                    $sheet->getCell("{$msCols[$i]}{$msRow}")->setValue($h);
                }
                $sheet->getStyle("A{$msRow}:G{$msRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $white], 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $navy]],
                ]);

                $msRow++;
                foreach ($memberships as $i => $m) {
                    $sheet->getCell("A{$msRow}")->setValue($m->created_at->format('d/m/Y H:i'));
                    $sheet->getCell("B{$msRow}")->setValue($m->plate);
                    $sheet->getCell("C{$msRow}")->setValue(ucfirst($m->vehicle_type));
                    $sheet->getCell("D{$msRow}")->setValue($m->start_date->format('d/m/Y'));
                    $sheet->getCell("E{$msRow}")->setValue($m->end_date->format('d/m/Y'));
                    $sheet->getCell("G{$msRow}")->setValue((float) $m->amount_paid);
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$msRow}:G{$msRow}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $light]],
                        ]);
                    }
                    $sheet->getStyle("A{$msRow}:G{$msRow}")->getFont()->setSize(9)->getColor()->setRGB($text);
                    $msRow++;
                }

                if ($memberships->isEmpty()) {
                    $sheet->mergeCells("A{$msRow}:G{$msRow}");
                    $sheet->getCell("A{$msRow}")->setValue('No hay mensualidades en este período.');
                    $sheet->getStyle("A{$msRow}")->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('94a3b8');
                    $msRow++;
                }

                // Subtotal mensualidades
                $sheet->mergeCells("A{$msRow}:F{$msRow}");
                $sheet->getCell("A{$msRow}")->setValue('SUBTOTAL MENSUALIDADES');
                $sheet->getCell("G{$msRow}")->setValue((float) $totalMemberships);
                $sheet->getStyle("A{$msRow}:G{$msRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $navy]],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $navy]]],
                ]);
                $sheet->getStyle("G{$msRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $msRow += 2;

                // ========================================
                // BLOQUE 5: GRAN TOTAL
                // ========================================
                $sheet->mergeCells("A{$msRow}:F{$msRow}");
                $sheet->getCell("A{$msRow}")->setValue('GRAN TOTAL DEL MES');
                $sheet->getCell("G{$msRow}")->setValue((float) $grandTotal);
                $sheet->getStyle("A{$msRow}:G{$msRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => $white]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $navy]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                ]);
                $sheet->getStyle("G{$msRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getRowDimension($msRow)->setRowHeight(28);

                // ========================================
                // BLOQUE 6: FOOTER
                // ========================================
                $msRow += 2;
                $sheet->mergeCells("A{$msRow}:G{$msRow}");
                $sheet->getCell("A{$msRow}")->setValue('ParkiApp — Sistema de Gestión de Parqueaderos — ' . $this->months[$this->month - 1] . ' ' . $this->year);
                $sheet->getStyle("A{$msRow}")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '94a3b8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $border]]],
                ]);

                // Ancho de columnas
                $sheet->getColumnDimension('A')->setWidth(20); // fecha
                $sheet->getColumnDimension('B')->setWidth(14); // placa
                $sheet->getColumnDimension('C')->setWidth(18); // tipo
                $sheet->getColumnDimension('D')->setWidth(18); // método / inicio
                $sheet->getColumnDimension('E')->setWidth(15); // fin
                $sheet->getColumnDimension('F')->setWidth(2);  // separador
                $sheet->getColumnDimension('G')->setWidth(16); // monto
            },
        ];
    }

    private function summaryCard($sheet, $row, $col1, $col2, $label, $value, $fontColor, $bgColor, $labelColor)
    {
        $rLabel = $row;
        $rValue = $row + 1;

        $sheet->mergeCells("{$col1}{$rLabel}:{$col2}{$rLabel}");
        $sheet->mergeCells("{$col1}{$rValue}:{$col2}{$rValue}");

        $sheet->getCell("{$col1}{$rLabel}")->setValue(strtoupper($label));
        $sheet->getCell("{$col1}{$rValue}")->setValue((float) $value);

        $sheet->getStyle("{$col1}{$rLabel}:{$col2}{$rLabel}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => $fontColor === 'FFFFFF' ? 'a5b4fc' : '64748b']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("{$col1}{$rValue}:{$col2}{$rValue}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => $fontColor]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle("{$col1}{$rValue}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getRowDimension($rLabel)->setRowHeight(16);
        $sheet->getRowDimension($rValue)->setRowHeight(28);
    }
}
