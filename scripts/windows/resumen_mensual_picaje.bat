@echo off
powershell -NoProfile -Command ^
  "Invoke-WebRequest ^
    'http://localhost/dolibarr/public/cron/cron_run_jobs_by_url.php?securitykey=clave_segura_123&userlogin=root&id=23' ^
    -UseBasicParsing | Out-Null"
