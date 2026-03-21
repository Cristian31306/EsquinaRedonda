<?php

/**
 * Script de limpieza de base de datos para Esquina Redonda
 * Uso: php limpiarbd.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap(); // <--- IMPORTANTE para que las Facades funcionen

echo "\n--- LIMPIEZA DE BASE DE DATOS (ESQUINA REDONDA) ---\n";
echo "Esta accion borrara TICKETS, PAGOS, MENSUALIDADES y TURNOS.\n";
echo "Se conservaran USUARIOS, TARIFAS y CONFIGURACION.\n\n";
echo "Escribe 'SI' para continuar: ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);

if (trim(strtoupper($line)) != 'SI') {
    echo "\nOperacion cancelada.\n";
    exit;
}

echo "\nIniciando limpieza...\n";

// Ejecutar el comando de Artisan que ya creamos
$exitCode = Artisan::call('app:clear-data', [
    '--force' => true,
]);

echo Artisan::output();
echo "\nProceso finalizado.\n";
