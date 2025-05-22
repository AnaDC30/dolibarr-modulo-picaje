#!/bin/bash

URL="http://localhost/public/cron/cron_run_jobs_by_url.php?securitykey=clave_segura_123&userlogin=root&id=23"

# Ejecutar sin mostrar salida
curl -s "$URL" > /dev/null
