<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;

class TelegramBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un backup de la base de datos MySQL, la comprime en ZIP y la envía por Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de backup...');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');

        $date = Carbon::now('America/Bogota')->format('Y-m-d_H-i-s');
        $sqlFileName = "backup_{$database}_{$date}.sql";
        $zipFileName = "{$sqlFileName}.zip";

        $storagePath = storage_path('app/backups');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $sqlPath = "{$storagePath}/{$sqlFileName}";
        $zipPath = "{$storagePath}/{$zipFileName}";

        // Construir el comando mysqldump compatible con Windows y Linux
        $passwordArg = empty($password) ? '' : "-p\"{$password}\"";
        
        $command = "mysqldump -h {$host} -P {$port} -u {$username} {$passwordArg} {$database} > \"{$sqlPath}\"";

        $this->info('Ejecutando volcado MySQL...');
        exec($command, $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($sqlPath) || filesize($sqlPath) === 0) {
            $this->error("Fallo al crear el volcado de la base de datos. Verifica que 'mysqldump' esté instalado.");
            return Command::FAILURE;
        }

        // Comprimir en ZIP
        $this->info('Comprimiendo en ZIP...');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($sqlPath, $sqlFileName);
            $zip->close();
        } else {
            $this->error('No se pudo crear el archivo ZIP.');
            return Command::FAILURE;
        }

        // Enviar a Telegram
        $this->info('Enviando a Telegram...');
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$botToken || !$chatId) {
            $this->error('Faltan credenciales de Telegram en el archivo .env o en el caché de config.');
            return Command::FAILURE;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendDocument";
        $appName = config('app.name');
        $caption = "🔒 Backup Automático DB\n🗄 App: {$appName}\n📅 Fecha: " . Carbon::now('America/Bogota')->format('d/M/Y H:i A') . "\n📦 DB: {$database}";

        $response = Http::attach(
            'document', file_get_contents($zipPath), $zipFileName
        )->post($url, [
            'chat_id' => $chatId,
            'caption' => $caption,
        ]);

        if ($response->successful()) {
            $this->info('¡Backup enviado a Telegram exitosamente!');
        } else {
            $this->error('Error enviando a Telegram: ' . $response->body());
        }

        // Limpiar archivos locales para ahorrar espacio
        $this->info('Limpiando archivos temporales...');
        @unlink($sqlPath);
        @unlink($zipPath);

        return Command::SUCCESS;
    }
}
