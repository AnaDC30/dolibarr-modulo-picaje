<?php
if (!defined('DOL_DOCUMENT_ROOT')) {
    die('Acceso no autorizado');
}

// =====================
//  CARGA DEL ENTORNO
// =====================
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once dol_buildpath('/custom/picaje/lib/dbController.php', 0);

// Cargar el estilo específico para esta vista
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/picaje.css', 1) . '">';

// Cabecera de Dolibarr

// =====================
//  DATOS NECESARIOS
// =====================

$registros = obtenerRegistrosDiarios();

// Verificar si el usuario ya ha picado hoy
$ha_entrada = false;
$ha_salida = false;

foreach ($registros as $r) {
    if ($r['tipo'] === 'entrada') $ha_entrada = true;
    if ($r['tipo'] === 'salida') $ha_salida = true;
}

// Obtener el token CSRF de Dolibarr
$token = newToken();
?>

<header class="page-header">
    <h1>Registro de Picaje</h1>
</header>

<div class="main-content">
    <h2>Registro de Picaje</h2>

    <?php if ($ha_entrada && $ha_salida): ?>
        <p>✅ Ya has registrado entrada y salida hoy.</p>
    <?php else: ?>
        <form method="post" action="<?php echo dol_buildpath('/custom/picaje/core/modules/procesar_picaje.php', 1); ?>" id="form-picaje">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <input type="hidden" name="tipo" id="tipo_picaje">
            <input type="hidden" name="latitud" id="latitud">
            <input type="hidden" name="longitud" id="longitud">

            <button type="submit" class="picajeButton" id="boton-picar">📍 Picar</button>
        </form>
    <?php endif; ?>
</div>

<div class="main-content">
    <h2>Registro Diario (<?php echo date("d/m/Y"); ?>)</h2>
    <table class="customTable">
        <tr>
            <th>Hora</th>
            <th>Tipo</th>
            <th>Origen</th>
        </tr>

        <?php if (empty($registros)): ?>
            <tr>
                <td colspan="3">No hay registros hoy.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($registros as $registro): ?>
                <tr>
                    <td><?php echo dol_escape_htmltag($registro['hora']); ?></td>
                    <td><?php echo ucfirst(dol_escape_htmltag($registro['tipo'])); ?></td>
                    <td><em><?php echo dol_escape_htmltag($registro['origen']); ?></em></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<div id="modalJustificacion" style="display: none;" class="modal-justificacion">
    <div class="modal-contenido">
        <h3>✏️ Justificación de salida anticipada</h3>
        <p>Tu hora de salida prevista aún no ha llegado. Indica el motivo por el cual deseas registrar la salida:</p>
        <textarea id="textoJustificacion" placeholder="Escribe aquí tu motivo..." rows="4"></textarea>
        <div class="modal-buttons">
            <button type="button" onclick="enviarJustificacion()">✅ Confirmar</button>
            <button type="button" onclick="cerrarModalJustificacion()">❌ Cancelar</button>
        </div>
    </div>
</div>

<input type="hidden" name="justificacion" id="inputJustificacion">

<script src="<?php echo dol_buildpath('/custom/picaje/js/picaje.js', 1); ?>"></script>

<script>
window.addEventListener('DOMContentLoaded', function () {
    const haEntrada = <?php echo $ha_entrada ? 'true' : 'false'; ?>;
    const haSalida = <?php echo $ha_salida ? 'true' : 'false'; ?>;

    inicializarPicaje(haEntrada, haSalida);
});
</script>

<!-- ===================== -->
<!--     BOTÓN VOLVER      -->
<!-- ===================== -->
<div class="picajeBack">
    <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php', 1); ?>" class="picajeArrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-big-left-icon lucide-arrow-big-left">
            <path d="M18 15h-6v4l-7-7 7-7v4h6v6z"/>
        </svg>
    </a>
</div>

