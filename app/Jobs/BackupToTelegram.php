<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use App\Models\CashShift;
use Illuminate\Support\Facades\Log;

class BackupToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 50; // Multiple retries for offline scenarios
    public $backoff = 60; // Wait 1 minute between retries

    protected $shiftId;

    /**
     * Create a new job instance.
     */
    public function __construct($shiftId = null)
    {
        $this->shiftId = $shiftId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = Setting::where('key', 'telegram_bot_token')->value('value');
        $chatIds = Setting::where('key', 'telegram_chat_ids')->value('value');

        if (!$token || !$chatIds) {
            Log::warning('Telegram backup skipped: Token or Chat IDs not configured.');
            return;
        }

        $chatIdsArray = array_map('trim', explode(',', $chatIds));
        $dbPath = database_path('database.sqlite');

        if (!file_exists($dbPath)) {
            Log::error('Telegram backup failed: database.sqlite not found.');
            return;
        }

        $message = "📝 *Resumen de Cierre de Turno*\n\n";
        
        if ($this->shiftId) {
            $shift = CashShift::find($this->shiftId);
            if ($shift) {
                $message .= "🆔 *Turno:* #{$shift->id}\n";
                $message .= "👤 *Responsable:* {$shift->user->name}\n";
                $message .= "📅 *Inicio:* " . $shift->start_time->format('d/m/Y H:i') . "\n";
                $message .= "📅 *Fin:* " . $shift->end_time->format('d/m/Y H:i') . "\n\n";
                $message .= "💰 *Detalle de Ingresos:*\n";
                $message .= "💵 *Efectivo:* $" . number_format($shift->total_cash, 0, ',', '.') . "\n";
                $message .= "💳 *Transferencia:* $" . number_format($shift->total_transfer, 0, ',', '.') . "\n";
                $message .= "📊 *Total General:* $" . number_format($shift->total_collected, 0, ',', '.') . "\n\n";
                $message .= "🏧 *Declarado (Efectivo):* $" . number_format($shift->closing_cash_declared, 0, ',', '.') . "\n";
                $diff = $shift->closing_cash_declared - $shift->total_cash;
                $message .= "⚖️ *Diferencia:* $" . number_format($diff, 0, ',', '.') . ($diff == 0 ? " ✅" : ($diff > 0 ? " ➕" : " ⚠️")) . "\n";
            }
        } else {
            $message .= "Respaldo manual de base de datos solicitado.";
        }

        foreach ($chatIdsArray as $chatId) {
            // Send Document (Database) with the summary as caption
            $response = Http::timeout(60)->withoutVerifying()->attach(
                'document', file_get_contents($dbPath), 'database_backup_' . date('Y-m-d_H-i-s') . '.sqlite'
            )->post("https://api.telegram.org/bot{$token}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => mb_substr($message, 0, 1024),
                'parse_mode' => 'Markdown',
            ]);

            // If it failed because of API rate limits or similar, throw so it retries
            // Http::post doesn't throw by default unless there is a connection timeout.
            // But we should throw on 5xx or specific 4xx (like rate limited) to retry
            if (!$response->successful() && $response->serverError()) {
                $response->throw();
            }
        }
    }
}
