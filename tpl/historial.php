<?php

// =====================
//  ENTORNO DE DOLIBARR
// =====================
require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once dol_buildpath('/custom/picaje/lib/dbController.php', 0);

// =====================
//  CARGAR ESTILOS CSS
// =====================
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/style.css.php">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/layout.css', 1) . '">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/modal.css', 1) . '">';

// =====================
//   OBTENER DATOS BBDD
// =====================
$filtroFecha = GETPOST('fecha', 'alpha');
$filtroUsuario = GETPOST('usuario', 'alpha');
$filtroUserId = GETPOST('user_id', 'int');   
$desdeIncidencias = GETPOST('desde', 'alpha');

if ($desdeIncidencias === 'incidencias') {
    $historial = obtenerHistorialPicajes(null, null, $filtroUserId); 
} else {
    $historial = obtenerHistorialPicajes($filtroFecha, $filtroUsuario); 
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
        <?php echo $langs->trans("Historial de Picaje"); ?>
    </span>
</div>


<!-- ===================== -->
<!--  CONTENEDOR PRINCIPAL -->
<!-- ===================== -->

<div class="fiche" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
    <!-- Botón filtros a la izquierda -->
    <div>
        <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all" onclick="toggleFiltros()">
            🔍 Filtros
        </button>
    </div>
    <div id="botonQuitarFlotante" class="oculto">
        <a href="picajeindex.php?view=historial" class="ui-button ui-widget ui-state-default ui-corner-all">🔄 Quitar filtros</a>
    </div>

    <!-- Botón Crear Incidencia a la derecha (solo si es admin) -->
    <?php if ($user->admin == 1): ?>
        <div>
            <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all" onclick="abrirModalCrearPicaje()">
                ⚠️ Incidencia en picaje
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- ===================== -->
<!--  CONTENEDOR FILTROS   -->
<!-- ===================== -->
<div id="filtrosContainer" class="fiche <?php echo ($filtroFecha || $filtroUsuario) ? '' : 'oculto'; ?>" style="margin-bottom: 15px;">
    <form method="GET" action="picajeindex.php" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end;">
        <input type="hidden" name="view" value="historial">

        <!-- Campo Fecha -->
        <div>
            <label for="fecha">Fecha:</label><br>
            <input type="date" name="fecha" id="fecha"
                   class="flat ui-widget ui-corner-all"
                   value="<?php echo dol_escape_htmltag($filtroFecha); ?>">
        </div>

        <!-- Campo Usuario (solo admin) -->
        <?php if ($user->admin == 1): ?>
            <div>
                <label for="usuario">Usuario:</label><br>
                <input type="text" name="usuario" id="usuario"
                       placeholder="Nombre o apellido"
                       class="flat ui-widget ui-corner-all"
                       value="<?php echo dol_escape_htmltag($filtroUsuario); ?>">
            </div>
        <?php endif; ?>

        <!-- Botón Buscar -->
        <div>
            <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all">🔍 Buscar</button>
        </div>
    </form>
</div>

<!-- Botones para exportar-->
<?php
$params = http_build_query([
    'fecha' => $filtroFecha,
    'usuario' => $filtroUsuario,
    'user_id' => GETPOST('user_id', 'int'),
    'desde' => GETPOST('desde', 'alpha')
]);
?>
<div class="fiche" style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
    
    <a href="export/exportar_pdf.php?<?php echo $params; ?>" class="ui-button ui-widget ui-state-default ui-corner-all">📄 Exportar PDF</a>
    <a href="export/exportar_csv.php?<?php echo $params; ?>" class="ui-button ui-widget ui-state-default ui-corner-all">📤 Exportar a CSV</a>
</div>


<!-- TABLA  -->
<div class="div-table-responsive">
    <table class="noborder allwidth">
        <thead class="liste_titre">
            <tr>
                <?php if ($user->admin == 1): ?>
                    <th class="center">Usuario</th>
                <?php endif; ?>
                <th class="center">Tipo</th>
                <th class="center">Hora</th>
                <th class="center">Fecha</th>
                <th class="center">Origen</th>
                <th class="center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($historial)): ?>
                <tr class="oddeven">
                    <td colspan="<?php echo ($user->admin == 1) ? 6 : 5; ?>" class="opacitymedium center">
                        ⚠️ No hay registros disponibles.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($historial as $registro): ?>
                    <tr class="oddeven valignmiddle fila-dinamica">
                        <?php if ($user->admin == 1): ?>
                            <td class="center"><?php echo dol_escape_htmltag($registro['usuario']); ?></td>
                        <?php endif; ?>
                        <td class="center"><?php echo dol_escape_htmltag($registro['tipo']); ?></td>
                        <td class="center"><?php echo dol_escape_htmltag($registro['hora']); ?></td>
                        <td class="center"><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></td>
                        <td class="center"><?php echo dol_escape_htmltag($registro['tipo_registro'] ?? 'manual'); ?></td>
                        <td class="center nowrap">
                            <button class="ui-button ui-widget" onclick="verUbicacion(<?php echo (int)$registro['id']; ?>)">📍</button>
                            <?php if ($user->admin == 1): ?>
                                <button class="ui-button ui-widget" onclick="abrirModalEditar(<?php echo (int)$registro['id']; ?>)">✏️</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="center" style="margin-top: 10px;">
    <button id="btn-mostrar-mas" class="button small">Mostrar más</button>
    </div>
</div>


<!-- MODALES -->
<?php include_once dol_buildpath('/custom/picaje/tpl/modales.php', 0); ?>


<div id="modalUbicacion" class="modal-overlay" style="display: none;">
    <div class="modal-content" id="modalUbicacionContenido"></div>
</div>


<script>
    const URL_GET_UBICACION = '<?php echo dol_buildpath("/custom/picaje/ajax/get_ubicacion.php", 1); ?>';
    const URL_GET_PICAJE = '<?php echo dol_buildpath("/custom/picaje/ajax/get_picaje.php", 1); ?>';
    const URL_MODIFICAR_PICAJE = '<?php echo dol_buildpath("/custom/picaje/ajax/modificar_picaje.php", 1); ?>';
    const URL_LOG_MODIFICACIONES = '<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=log_modificaciones', 1); ?>';
    const URL_GET_CREAR_PICAJE = '<?php echo dol_buildpath("/custom/picaje/ajax/procesar_picaje_incidencia.php", 1); ?>';
</script>

<script src="<?php echo dol_buildpath('/custom/picaje/js/picaje.js', 1); ?>"></script>

<script>
    const DOLIBARR_CSRF_TOKEN = '<?php echo newToken(); ?>';

    function toggleFiltros() {
        const contenedor = document.getElementById("filtrosContainer");
        contenedor.classList.toggle("oculto");
    }

     // Mostrar botón flotante de quitar filtros si hay filtros activos
     document.addEventListener("DOMContentLoaded", function () {
    const fecha = "<?php echo dol_escape_htmltag($filtroFecha); ?>";
    const usuario = "<?php echo dol_escape_htmltag($filtroUsuario); ?>";

    const tieneFiltros = fecha.length > 0 || usuario.length > 0;
    const botonFlotante = document.getElementById("botonQuitarFlotante");

    if (tieneFiltros && botonFlotante) {
        botonFlotante.classList.remove("oculto");
    } else if (botonFlotante) {
        botonFlotante.classList.add("oculto");
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const filas = document.querySelectorAll(".fila-dinamica");
  const btn = document.getElementById("btn-mostrar-mas");
  const batchSize = 10;
  let mostradas = 0;

  function mostrarMas() {
    for (let i = mostradas; i < mostradas + batchSize && i < filas.length; i++) {
      filas[i].style.display = "";
    }
    mostradas += batchSize;
    if (mostradas >= filas.length && btn) {
      btn.style.display = "none";
    }
  }

  filas.forEach((fila, i) => {
    fila.style.display = i < batchSize ? "" : "none";
  });

  if (btn) btn.addEventListener("click", mostrarMas);
});
</script>


