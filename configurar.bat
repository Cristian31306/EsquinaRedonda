@echo off
setlocal enabledelayedexpansion

:: Colores simples (simulados con echo)
echo ====================================================
echo    CONFIGURACION DE ENTORNO - ESQUINA REDONDA
echo ====================================================

:: 1. Verificar si PHP existe
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [!] PHP no se detecto en el sistema.
    echo [i] Se intentara instalar Laragon y Node.js automaticamente...
    
    :: Intentar usar winget
    where winget >nul 2>nul
    if %errorlevel% equ 0 (
        echo [OK] Usando winget para instalar herramientas...
        winget install Laragon.Laragon OpenJS.NodeJS.LTS --silent --accept-package-agreements --accept-source-agreements
        echo.
        echo [!] HERRAMIENTAS INSTALADAS. 
        echo [TIP] Por favor, CIERRA esta ventana y vuelve a abrirla para que 
        echo       los cambios surtan efecto y PHP este disponible.
    ) else (
        echo [ERROR] No se encontro 'winget'. Por favor instala Laragon y Node.js manualmente.
        echo Consulta el README.md para los enlaces de descarga.
    )
    pause
    exit /b
)

:: 2. Si PHP existe, correr el instalador
echo [OK] PHP detectado. Iniciando instalador del proyecto...
php instalar.php

echo.
echo Operacion finalizada.
pause
