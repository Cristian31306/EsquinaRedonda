Set WshShell = CreateObject("WScript.Shell")
WshShell.CurrentDirectory = "C:\Users\CANAL ASESORES LTDA\Documents\Proyectos\EsquinaRedonda"
WshShell.Run "C:\Users\CANAL ASESORES LTDA\.config\herd-lite\bin\php.exe artisan serve --host=0.0.0.0 --port=8000", 0, False
