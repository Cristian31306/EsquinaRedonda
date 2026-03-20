<?php

/**
 * Script de Instalación Inicial para Esquina Redonda
 * Uso: php instalar.php
 */

function ejecutar($comando) {
    echo "\n\033[32m[EJECUTANDO]\033[0m: $comando\n";
    passthru($comando . ' 2>&1');
}

echo "\n\033[34m====================================================\033[0m\n";
echo "\033[34m    INSTALACIÓN INICIAL - ESQUINA REDONDA          \033[0m\n";
echo "\033[34m====================================================\033[0m\n";

// 1. Crear .env
if (!file_exists('.env')) {
    echo "\n\033[33mCreando archivo .env desde .env.example...\033[0m\n";
    if (copy('.env.example', '.env')) {
        echo "\033[32m✓ Archivo .env creado correctamente.\033[0m\n";
    }
} else {
    echo "\n\033[32mEl archivo .env ya existe.\033[0m\n";
}

// 2. Composer install
echo "\n\033[33mInstalando dependencias de PHP (Composer)...\033[0m\n";
ejecutar('composer install');

// 3. Generar App Key
echo "\n\033[33mGenerando clave de la aplicación...\033[0m\n";
ejecutar('php artisan key:generate');

// 4. Crear base de datos SQLite si no existe
$dbPath = __DIR__ . '/database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "\n\033[33mCreando base de datos SQLite en $dbPath...\033[0m\n";
    touch($dbPath);
    echo "\033[32m✓ Base de datos creada.\033[0m\n";
}

// 5. Migraciones y Seeders
echo "\n\033[33mEjecutando migraciones y cargando datos iniciales...\033[0m\n";
ejecutar('php artisan migrate:fresh --seed');

// 6. NPM install
echo "\n\033[33mInstalando dependencias de JS (NPM)...\033[0m\n";
ejecutar('npm install --legacy-peer-deps');

// 7. NPM build
echo "\n\033[33mCompilando assets con Vite...\033[0m\n";
ejecutar('npm run build');

echo "\n\033[34m====================================================\033[0m\n";
echo "\033[32m       ¡INSTALACIÓN COMPLETADA CON ÉXITO!          \033[0m\n";
echo "\033[32m   Acceso: admin / 123456                          \033[0m\n";
echo "\033[34m====================================================\033[0m\n\n";
echo "Para iniciar el servidor ejecuta: php artisan serve\n\n";
