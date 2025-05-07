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


$entradaAutomaticaActiva = !empty($conf->global->PICAJE_AUTO_LOGIN);


// 3) Cargar estilos
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/picaje.css', 1) . '">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/modal.css', 1) . '">';

// =====================
//  OPCIONES DE PICAJE
// =====================

$registros = obtenerRegistrosDiarios();
$estado = Picaje::getEstadoHoy($db, $user->id);
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
<header class="page-header">
    <h1>Realizar Picaje</h1>
</header>

<div class="container-flex">
    <div class="main-content">
        <h2>Registro de Picaje</h2>

        <?php if ($mostrarBoton): ?>
            <form id="form-picaje" method="post">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="hidden" name="tipo" value="<?php echo $tipoRegistro; ?>">
                <input type="hidden" name="latitud" id="latitud">
                <input type="hidden" name="longitud" id="longitud">
                <input type="hidden" name="justificacion" id="inputJustificacion">

                <button type="submit" class="picajeButton" id="boton-picar" <?php echo $desactivarBoton; ?>>
                    <?php echo $textoBoton; ?>
                </button>
            </form>
        <?php else: ?>
            <p>✅ Ya has registrado entrada y salida hoy o la salida será automática.</p>
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
</div>

<footer>
    <?php
            $autoSalidaActiva = getDolGlobalInt('PICAR_SALIDA_AUTOMATICA');
            $horario = getHorarioUsuario($user->id);

            if ($autoSalidaActiva && $horario && $horario->hora_salida) {
                echo '
                <div class="salida-auto-alert">
                    🕒 <strong>Recuerda:</strong> La <strong>salida automática</strong> está activada hoy a las <strong>' . dol_escape_htmltag($horario->hora_salida) . '</strong>.<br>
                </div>';
            }
    ?>
</footer>

<!-- ======================================== -->
<!--   MODAL JUSTIFICACIÓN  ENTRADA Y SALIDA  -->
<!-- ======================================== -->
<div id="modalJustificacion" class="modal-overlay">
  <div class="modal-content">
    <button class="cerrarModal" onclick="cerrarModalJustificacion()">✖</button>
    <div class="modal-inner-form">
      <h2>✏️ Justificación de Picaje anticipado</h2>
      <p>Tu hora de entrada/salida prevista aún no ha llegado. Indica el motivo por el cual deseas registrar el picaje:</p>
      <form onsubmit="event.preventDefault(); enviarJustificacion();">
        <label>Tipo de incidencia:</label>
        <div class="toggle-group">
            <input type="radio" id="opcion_extra" name="tipoIncidencia" value="horas_extra" required hidden>
            <label for="opcion_extra" class="toggle-btn">Horas extra</label>
            
            <input type="radio" id="opcion_entrada_anticipada" name="tipoIncidencia" value="entrada_anticipada" required hidden>
            <label for="opcion_entrada_anticipada" class="toggle-btn">Entrada anticipada</label>

            <input type="radio" id="opcion_anticipada" name="tipoIncidencia" value="salida_anticipada" required hidden>
            <label for="opcion_anticipada" class="toggle-btn">Salida anticipada</label>

            <input type="radio" id="opcion_otro" name="tipoIncidencia" value="otro" required hidden>
            <label for="opcion_otro" class="toggle-btn">Otro</label>
        </div>
 
        <label for="textoJustificacion">Motivo:</label>
            <textarea id="textoJustificacion" placeholder="Escribe aquí tu motivo..." rows="4" required></textarea>

        <div class="modal-actions">
            <button type="button" onclick="cerrarModalJustificacion()">Cancelar</button>
            <button type="submit" class="guardarButton">Confirmar</button>
        </div>
      </form>
 
    </div>
  </div>
</div>

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
  const entradaManualJustificada = <?php echo $entrada_manual_justificada ? 'true' : 'false'; ?>;
  const entradaAutomaticaActiva = <?php echo $entradaAutomaticaActiva ? 'true' : 'false'; ?>;      

  inicializarPicaje(haEntrada, haSalida, salidaManualJustificada, salidaAutomaticaActiva, entradaManualJustificada, entradaAutomaticaActiva);
</script>


<!-- ===================== -->
<!--     BOTÓN VOLVER      -->
<!-- ===================== -->
<div class="backContainer">
    <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php', 1); ?>" class="backArrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-big-left-icon lucide-arrow-big-left">
            <path d="M18 15h-6v4l-7-7 7-7v4h6v6z"/>
        </svg>
    </a>
</div>



