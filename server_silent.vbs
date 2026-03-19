Set WshShell = CreateObject("WScript.Shell")
WshShell.CurrentDirectory = "C:\Users\LENOVO LOQ\Documents\Proyectos\EsquinaRedonda"
WshShell.Run """C:\Users\LENOVO LOQ\.config\herd-lite\bin\php.exe"" artisan serve --host=0.0.0.0 --port=8000", 0, False
