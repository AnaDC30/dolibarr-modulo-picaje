@echo off
set LOGFILE=C:\xampp\htdocs\dolibarr\cron\autoexit_debug.log

echo [%date% %time%] Iniciando autoexit >> %LOGFILE% 2>&1

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Invoke-WebRequest 'http://localhost/dolibarr/public/cron/cron_run_jobs_by_url.php?securitykey=clave_segura_123&userlogin=root&id=25' -UseBasicParsing" ^
  >> %LOGFILE% 2>&1

echo ExitCode: %ERRORLEVEL% >> %LOGFILE%
echo ------------------------------ >> %LOGFILE%
