@echo off
echo Iniciando servidores...

:: Path to PHP used by Herd
set PHP_PATH="C:\Users\LENOVO LOQ\.config\herd-lite\bin\php.exe"

:: Start Laravel server in the background
start /B "Laravel Server" %PHP_PATH% artisan serve --host=0.0.0.0 --port=8000

:: Start Vite dev server in the background
start /B "Vite Dev Server" npm run dev

:: Wait a few seconds for servers to initialize
timeout /t 5 /nobreak > nul

:: Open the browser
start http://127.0.0.1:8000/

echo.
echo ==========================================
echo  Proyecto "Esquina Redonda" iniciado.
echo  - Servidor PHP: http://127.0.0.1:8000
echo  - Servidor Vite: Corriendo en segundo plano
echo ==========================================
echo Presiona cualquier tecla para cerrar esta ventana (los servidores continuaran).
pause > nul
