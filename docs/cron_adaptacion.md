# Guía de adaptación de cron para el módulo Picaje

Este módulo incluye scripts programables mediante cron para automatizar tareas como el registro de ausencias, picaje automático de entrada/salida, etc.

Dado que la forma de configurar tareas cron varía según el sistema operativo y el entorno, esta guía te explica cómo adaptarlos correctamente.

---

## 🔐 Parámetros importantes

Todos los scripts cron incluidos en este módulo acceden a Dolibarr mediante la URL:

```
http://localhost/public/cron/cron_run_jobs_by_url.php?securitykey=clave_segura_123&userlogin=root&id=XX
```

Donde:

- `securitykey=clave_segura_123`: clave de seguridad configurada en el cron
- `userlogin=root`: debe ser un usuario con permisos suficientes para ejecutar tareas cron
- `id=XX`: es el ID del trabajo programado en Dolibarr (puede consultarse en *Inicio > Administración del sistema > Tareas programadas*)

⚠️ **Es imprescindible que adaptes estos valores a tu instalación real**.

---

## 🖥️ En Windows (XAMPP u otros)

Usa los archivos `.bat` disponibles en:

```
custom/picaje/scripts/windows/
```

### 📌 Ejemplo:

```bat
@echo off
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Invoke-WebRequest 'http://localhost/dolibarr/public/cron/cron_run_jobs_by_url.php?securitykey=clave_segura_123&userlogin=root&id=23' -UseBasicParsing"
```

- Puedes programar estos `.bat` usando el **Programador de tareas de Windows**
- Asegúrate de modificar:
  - La ruta del PHP si lo ejecutas directamente
  - La URL según el dominio, clave y cron ID reales

---

## 🐧 En Linux o Docker

Usa los scripts `.sh` disponibles en:

```
custom/picaje/scripts/linux/
```

### 📌 Ejemplo:

```bash
#!/bin/bash
curl -s "http://localhost/public/cron/cron_run_jobs_by_url.php?securitykey=clave_segura_123&userlogin=root&id=23" > /dev/null
```

- Asegúrate de hacer ejecutables los archivos:
  ```bash
  chmod +x archivo.sh
  ```
- En Docker, usa `cron` dentro de un contenedor o llama a estos scripts desde el host mediante `docker exec`.

---

## ⚙️ En el módulo “Tareas programadas” de Dolibarr

Cuando configures las tareas desde **Inicio > Administración del sistema > Tareas programadas**, debes:

- Establecer el `comando` del cron como una **ruta válida y funcional en tu entorno**
- En Windows: puede ser algo como:
  ```
  C:\xampp\php\php.exe C:\xampp\htdocs\dolibarr\custom\picaje\scripts\registrar_ausencias_diarias.php
  ```
- En Docker o Linux:
  ```
  php /var/www/html/custom/picaje/scripts/registrar_ausencias_diarias.php
  ```

---

## 🧩 Recomendación

Para entornos multiplataforma, deja tus `.bat` y `.sh` listos para ser modificados, y documenta claramente los campos que el usuario debe ajustar (URL, clave, usuario, cron ID).