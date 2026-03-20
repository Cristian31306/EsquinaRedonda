<?php

/**
 * Script de Automatización de Despliegue para Esquina Redonda
 * Uso: php actualizar.php
 */

function ejecutar($comando) {
    echo "\n\033[32m[EJECUTANDO]\033[0m: $comando\n";
    passthru($comando . ' 2>&1');
}

echo "\n\033[34m====================================================\033[0m\n";
echo "\033[34m    INICIANDO ACTUALIZACIÓN - ESQUINA REDONDA       \033[0m\n";
echo "\033[34m====================================================\033[0m\n";

// 1. Mantenimiento
echo "\n\033[33mEntrando en modo mantenimiento...\033[0m\n";
ejecutar('php artisan down');

// 2. Git pull
echo "\n\033[33mObteniendo últimos cambios de Git...\033[0m\n";
ejecutar('git pull origin main');

// 3. Composer install
echo "\n\033[33mInstalando dependencias de PHP...\033[0m\n";
ejecutar('composer install --no-interaction --prefer-dist --optimize-autoloader');

// 4. Migraciones
echo "\n\033[33mEjecutando migraciones de base de datos...\033[0m\n";
ejecutar('php artisan migrate --force');

// 5. Dependencias JS
echo "\n\033[33mInstalando dependencias de NPM...\033[0m\n";
ejecutar('npm install');

// 6. Construir Assets
echo "\n\033[33mConstruyendo assets para producción...\033[0m\n";
ejecutar('npm run build');

// 7. Optimizar
echo "\n\033[33mLimpiando y optimizando caché...\033[0m\n";
ejecutar('php artisan optimize:clear');
ejecutar('php artisan optimize');

// 8. Salir de mantenimiento
echo "\n\033[33mSaliendo del modo mantenimiento...\033[0m\n";
ejecutar('php artisan up');

echo "\n\033[34m====================================================\033[0m\n";
echo "\033[32m       ¡ACTUALIZACIÓN COMPLETADA CON ÉXITO!        \033[0m\n";
echo "\033[34m====================================================\033[0m\n\n";
