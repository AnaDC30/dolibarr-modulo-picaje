<?php
// =====================
//  CARGA DEL ENTORNO
// =====================

// 1) Cargar Dolibarr (main.inc.php)
if (!defined('DOL_DOCUMENT_ROOT')) {
    // Está a dos niveles: /custom/picaje/tpl/picaje.php
    require_once dirname(__DIR__, 3) . '/main.inc.php';
}

// 2) Asegurar globals y librerías
global $db, $user, $conf, $langs;
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/class/picaje.class.php';



// 3) Cargar estilos
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/style.css.php">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/layout.css', 1) . '">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/modal.css', 1) . '">';

// =====================
//  OPCIONES DE PICAJE
// =====================

$registros = obtenerRegistrosDiarios();
$estado = Picaje::getEstadoHoy($db, $user->id);
$horario = getHorarioUsuario($user->id);
$ha_entrada = $estado['entrada'];
$ha_salida = $estado['salida'];
$entrada_manual_justificada = getDolGlobalInt('PICAR_ENTRADA_ANTICIPADA_JUSTIFICADA');
$salida_automatica = getDolGlobalInt('PICAR_SALIDA_AUTOMATICA');
$salida_manual_justificada = getDolGlobalInt('PICAR_SALIDA_MANUAL_JUSTIFICADA');
$entradaAutomaticaActiva = getDolGlobalInt('PICAJE_AUTO_LOGIN');
// ================
//  TOKEN // CSRF
// ================

$token = newToken(); 

// =====================
//  LÓGICA BOTÓN PICAJE
// =====================

$mostrarBoton = false;
$tipoRegistro = '';
$textoBoton = '';
$desactivarBoton = '';

if (!$ha_entrada) {
    $tipoRegistro = 'entrada';
    $textoBoton = "📍 Picar entrada";
    $mostrarBoton = true;
} elseif ($ha_entrada && !$ha_salida) {
    if (!$salida_automatica || $salida_manual_justificada) {
        $tipoRegistro = 'salida';
        $textoBoton = "📍 Picar salida";
        $mostrarBoton = true;
    }
} elseif ($ha_entrada && $ha_salida) {
    $textoBoton = "✅ Picaje completado";
    $desactivarBoton = 'disabled';
}
?>

<!-- ===================== -->
<!--       ENCABEZADO      -->
<!-- ===================== -->
<div class="titre">
    <span class="inline-block valignmiddle">
        <?php echo img_picto('', 'picaje@picaje'); ?>
    </span>
    <span class="inline-block valignmiddle" style="font-size: 22px; font-weight: bold;">
        <?php echo $langs->trans("Panel Picaje"); ?>
    </span>
</div>

<div class="secciones-grid">

    <!-- Bloque Picaje actual -->
    <div class="seccion-modulo" style="width: 280px;">
        <div class="seccion-icono">🕒</div>
        <div class="seccion-titulo">Picaje actual</div>

        <?php if ($mostrarBoton): ?>
            <form id="form-picaje" method="post">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="hidden" name="tipo" value="<?php echo $tipoRegistro; ?>">
                <input type="hidden" name="latitud" id="latitud">
                <input type="hidden" name="longitud" id="longitud">
                <input type="hidden" name="justificacion" id="inputJustificacion">

                <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" id="boton-picar" <?php echo $desactivarBoton; ?>>
                    <?php echo $textoBoton; ?>
                </button>
            </form>
        <?php else: ?>
            <div class="opacitymedium">✅ Ya has registrado entrada y salida hoy o la salida será automática.</div>
        <?php endif; ?>
    </div>

    <!-- Bloque Registro Diario más ancho -->
    <div class="seccion-modulo" style="width: 300px; height: 300px;">
        <div class="seccion-icono">📋</div>
        <div class="seccion-titulo">Registro Diario</div>
        <div class="seccion-descripcion"><?php echo date("d/m/Y"); ?></div>

        <div class="div-table-responsive" style="margin-top: 10px;">
            <table class="noborder allwidth">
                <thead class="liste_titre">
                    <tr>
                        <th class="center">Hora</th>
                        <th class="center">Tipo</th>
                        <th class="center">Origen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registros)): ?>
                        <tr class="oddeven">
                            <td colspan="3" class="center opacitymedium">No hay registros hoy.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registros as $registro): ?>
                            <tr class="oddeven">
                                <td class="center"><?php echo dol_escape_htmltag($registro['hora']); ?></td>
                                <td class="center"><?php echo ucfirst(dol_escape_htmltag($registro['tipo'])); ?></td>
                                <td class="center"><em><?php echo dol_escape_htmltag($registro['origen']); ?></em></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>


<footer>
    <!-- Bloque info salida automática -->
 <?php if ($salida_automatica && $horario && $horario->hora_salida): ?>
    <div class="fiche center" style="margin-top: 10px;">
        <div class="info" style="display: inline-block; max-width: 600px;">
            🕒 <strong>Recuerda:</strong> La <strong>salida automática</strong> está activada hoy a las 
            <strong><?php echo dol_escape_htmltag($horario->hora_salida); ?></strong>.
        </div>
    </div>
 <?php endif; ?>
</footer>

<style>
    .seccion-modulo:hover {
        transform: none !important;
        box-shadow: none !important;
    }
</style>

<!--   MODAL JUSTIFICACIÓN  ENTRADA Y SALIDA  -->
<?php include_once dol_buildpath('/custom/picaje/tpl/modales.php', 0); ?>

<div id="toast" class="toast" style="display:none;"></div>

<!-- =============== -->
<!--  TOKEN Y CRSF   -->
<!-- =============== -->
<script>
  const csrfToken = '<?php echo $token; ?>';
</script>

<!-- ============================= -->
<!--   SCRIPT PRINCIPAL DEL MÓDULO -->
<!-- ============================= -->

<script src="<?php echo dol_buildpath('/custom/picaje/js/picaje.js', 1); ?>"></script>

<script>
  const haEntrada = <?php echo $ha_entrada ? 'true' : 'false'; ?>;
  const haSalida = <?php echo $ha_salida ? 'true' : 'false'; ?>;
  const salidaManualJustificada = <?php echo getDolGlobalInt('PICAR_SALIDA_MANUAL_JUSTIFICADA') ? 'true' : 'false'; ?>;
  const salidaAutomaticaActiva = <?php echo getDolGlobalInt('PICAR_SALIDA_AUTOMATICA') ? 'true' : 'false'; ?>;
  const entradaManualJustificada = <?php echo getDolGlobalInt('PICAR_ENTRADA_ANTICIPADA_JUSTIFICADA') ? 'true' : 'false'; ?>;
  const entradaAutomaticaActiva = <?php echo getDolGlobalInt('PICAR_AUTO_LOGIN') ? 'true' : 'false'; ?>;      


document.addEventListener('DOMContentLoaded', function () {
  inicializarPicaje(
    haEntrada,
    haSalida,
    salidaManualJustificada,
    salidaAutomaticaActiva,
    entradaManualJustificada,
    entradaAutomaticaActiva
  );
});


console.log("🚦 haEntrada:", haEntrada);
console.log("🚦 haSalida:", haSalida);
console.log("🚦 salidaManualJustificada:", salidaManualJustificada);
console.log("🚦 salidaAutomaticaActiva:", salidaAutomaticaActiva);

</script>




