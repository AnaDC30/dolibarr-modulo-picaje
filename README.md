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

## 📄 Licencia

- **Código**: GPLv3 o superior
- **Documentación**: GFDL 1.3
