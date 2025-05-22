<?php
// === Cargar entorno Dolibarr si no se ha hecho ya ===
if (!defined('DOL_DOCUMENT_ROOT')) {
    // Ajusta la ruta según la profundidad de tu archivo
    require_once dirname(__DIR__, 3) . '/main.inc.php';

}

// Ahora sí podemos usar DOLIBARR y sus globals
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
global $user, $conf, $db, $langs;

// Enlazar el CSS específico de esta vista
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/style.css.php">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/layout.css', 1) . '">';
?>


<div class="titre">
    <span class="inline-block valignmiddle">
        <?php echo img_picto('', 'picaje@picaje'); ?>
    </span>
    <span class="inline-block valignmiddle" style="font-size: 22px; font-weight: bold;">
        <?php echo $langs->trans("Picaje"); ?>
    </span>
</div>


<div class="secciones-grid">

    <!-- Picaje -->
    <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=picaje', 1); ?>" class="seccion-modulo">
        <div class="seccion-icono">🕒</div>
        <div class="seccion-titulo">Picaje</div>
        <div class="seccion-descripcion">Registrar tu entrada o salida diaria.</div>
    </a>

    <!-- Historial -->
    <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=historial', 1); ?>" class="seccion-modulo">
        <div class="seccion-icono">📋</div>
        <div class="seccion-titulo">Historial</div>
        <div class="seccion-descripcion">Consulta tus registros de picaje.</div>
    </a>

    <!-- Incidencias admin -->
    <?php if ($user->admin && $user->id == 1): ?>
        <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=incidencias', 1); ?>" class="seccion-modulo">
            <div class="seccion-icono">⚠️</div>
            <div class="seccion-titulo">Incidencias</div>
            <div class="seccion-descripcion">Revisión y gestión de incidencias registradas.</div>
        </a>
    <?php endif; ?>

    <!-- Incidencias usuario -->
    <?php if (!$user->admin): ?>
        <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=incidencias_user', 1); ?>" class="seccion-modulo">
            <div class="seccion-icono">📝</div>
            <div class="seccion-titulo">Mis Incidencias</div>
            <div class="seccion-descripcion">Consulta y reporta incidencias relacionadas con tus picajes.</div>
        </a>
    <?php endif; ?>

    <!-- Configuración -->
    <?php if ($user->admin): ?>
        <a href="<?php echo dol_buildpath('/custom/picaje/admin/setup.php', 1); ?>" class="seccion-modulo">
            <div class="seccion-icono">⚙️</div>
            <div class="seccion-titulo">Configuración</div>
            <div class="seccion-descripcion">Accede a la configuración general del módulo de picaje.</div>
        </a>
    <?php endif; ?>

</div>