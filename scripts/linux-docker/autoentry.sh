#!/bin/bash

LOGFILE="/var/www/html/cron/autoentry_debug.log"
URL="http://localhost/public/cron/cron_run_jobs_by_url.php?securitykey=clave_segura_123&userlogin=root&id=26"

echo "[$(date)] Iniciando picaje_autoentry" >> "$LOGFILE" 2>&1

curl -s "$URL" >> "$LOGFILE" 2>&1

echo "ExitCode: $?" >> "$LOGFILE"
echo "------------------------------" >> "$LOGFILE"
