# 🕘 Módulo de Picaje para Dolibarr ERP & CRM

Este módulo permite registrar, visualizar y gestionar los **fichajes de entrada y salida de los trabajadores** dentro de Dolibarr. Está diseñado para adaptarse a empresas con necesidades de control horario, justificaciones, automatismos y reportes mensuales.

Repositorio oficial: [github.com/AnaDC30/dolibarr-modulo-picaje](https://github.com/AnaDC30/dolibarr-modulo-picaje)

---

## 🚀 Funcionalidades Principales

- ✅ Registro de entrada/salida manual y automático
- 📍 Geolocalización obligatoria del fichaje
- 🧠 Picaje inteligente: un solo botón que adapta el tipo (entrada o salida)
- 📄 Justificación de salidas y entradas anticipadas
- 📝 Gestión de incidencias por olvido o motivo especial
- 🔐 Roles y permisos diferenciados (root, usuarios)
- 🧾 Reportes mensuales en PDF con firma digital y envío por email
- 🕵️‍♂️ Historial completo de fichajes con log de modificaciones
- 📦 Integración con días de vacaciones (módulo Holiday)
- 🔁 Picaje desde panel lateral de Dolibarr

---

## 🧩 Requisitos

- Dolibarr ERP/CRM **v21** o superior
- PHP 7.4+
- Módulo `Holiday` activado (opcional pero recomendado)
- Mailjet o SMTP configurado en Dolibarr para el envío de correos

---

## 🛠 Instalación

### 📦 Desde archivo ZIP

1. Descarga o genera el ZIP del módulo.
2. En Dolibarr: ve a **Inicio → Configuración → Módulos → Instalar módulo externo**.
3. Sube el archivo `.zip`.
4. Activa el módulo desde la lista.

### 🧬 Desde GIT (modo desarrollador)

```bash
cd htdocs/custom
git clone https://github.com/AnaDC30/dolibarr-modulo-picaje.git picaje
```

---

## ⚙️ Configuración

Una vez activado el módulo, puedes:

- Asignar **permisos personalizados** por usuario o grupo.
- Configurar opciones desde la sección **Configuración del módulo Picaje**:

  1. Activar picaje automático al iniciar sesión
  2. Activar salida automática
  3. Definir la duración de la jornada laboral
  4. Seleccionar el modo de horario: por usuario o por grupo

---

## 🖥 Panel del Usuario

Cada usuario puede:

- Registrar su entrada/salida con un solo botón
- Visualizar su historial diario y completo
- Justificar una incidencia (salida anticipada, olvido, horas extra)
- Consultar sus incidencias pendientes y resueltas

---

## 🛡 Panel del Administrador

El administrador (root) puede:

- Ver y editar todos los registros de fichaje
- Consultar el log de modificaciones
- Revisar y resolver incidencias
- Registrar picajes olvidados desde incidencias
- Acceder a reportes mensuales y firmarlos digitalmente

---

## 📤 Reporte mensual automático

- Generación de PDF personalizado el **día 1 de cada mes**
- Envío automático por correo a cada trabajador
- Estructura clara del informe, que incluye:
  - Registros diarios
  - Horas normales
  - Horas extra
  - Salidas anticipadas
- Firma digital opcional mediante certificados `.pem`

---

## 📚 Estructura de Archivos

```
dolibarr-modulo-picaje/
├── ajax/
├── css/
├── exports/
├── lib/
├── scripts/
├── tpl/
├── README.md
├── modPicaje.class.php
├── dbController.php
...
```

---

# ⚠️ Instrucciones para importar el archivo SQL del módulo Picaje

Este módulo incluye un archivo SQL con la estructura necesaria para crear sus tablas personalizadas (`llx_picaje`, `llx_incidencias`, etc.).

---

## 📁 Ubicación del archivo

```
custom/picaje/sql/install.sql
```

---

## 🛠️ ¿Cuándo debes usar este archivo?

- Si al activar el módulo no se crean automáticamente las tablas
- Si estás migrando el módulo a otro entorno o reinstalándolo
- Si necesitas regenerar las tablas por algún fallo o corrupción

---

## ✅ ¿Cómo importar el archivo?

### Opción 1: Usando phpMyAdmin

1. Accede a phpMyAdmin
2. Selecciona la base de datos activa (ej. `dolibarr`)
3. Ve a la pestaña **Importar**
4. Selecciona el archivo `install.sql`
5. Haz clic en **Continuar**

### Opción 2: Usando línea de comandos

#### En Linux/Docker

```bash
mysql -u root -p dolibarr < custom/picaje/sql/install.sql
```

#### En Windows (XAMPP)

```bat
C:\xampp\mysql\bin\mysql.exe -u root -p dolibarr < C:\xampp\htdocs\dolibarr\custom\picaje\sql\install.sql
```

(Recuerda ajustar la ruta según tu instalación)

---

## ⚠️ Advertencias importantes

- Ejecuta este archivo **solo una vez**
- Si las tablas ya existen, puede generar errores de duplicado
- Asegúrate de que estás conectado a la **base de datos correcta** antes de importarlo

---

## 📎 Recomendación

Documenta en tu entorno cuándo y cómo se ha ejecutado este archivo para evitar reimportaciones accidentales.

---

### ⚠️ Importante sobre tareas programadas (cron)

Este módulo incluye scripts que pueden ser ejecutados automáticamente mediante tareas programadas (cron).  
Debido a las diferencias entre sistemas operativos, encontrarás ejemplos preparados tanto para **Windows (.bat)** como para **Linux/Docker (.sh)**.

📄 Consulta el archivo [`docs/cron_adaptacion.md`](docs/cron_adaptacion.md) para ver cómo configurar correctamente los cron en cada entorno.


---

## 📄 Licencia

- **Código**: GPLv3 o superior
- **Documentación**: GFDL 1.3
