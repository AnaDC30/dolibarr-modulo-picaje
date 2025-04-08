<?php

// =====================
//  ENTORNO DE DOLIBARR
// =====================
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once dol_buildpath('/custom/picaje/lib/dbController.php', 0);

// =====================
//  CARGAR ESTILOS CSS
// =====================
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/historial.css', 1) . '">';
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
<header class="page-header">
    <h1>Historial de Picaje</h1>
</header>

<!-- ===================== -->
<!--  CONTENEDOR PRINCIPAL -->
<!-- ===================== -->

<!-- BOTON FILTRO -->
<button type="button" class="toggle-filtros" onclick="toggleFiltros()">🔍</button>
<div id="botonQuitarFlotante" class="btn-reset-flotante oculto">
    <a href="picajeindex.php?view=historial" class="btn-reset">🔄 Quitar filtros</a>
</div>

<!-- Contenedor del formulario oculto inicialmente -->
<div id="filtrosContainer" class="filtro-formulario oculto">
    <form method="GET" action="picajeindex.php">
        <input type="hidden" name="view" value="historial">
        <div class="filtros">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" value="<?php echo dol_escape_htmltag($filtroFecha); ?>">

            <?php if ($user->admin == 1): ?>
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" placeholder="Nombre o apellido" value="<?php echo dol_escape_htmltag($filtroUsuario); ?>">
            <?php endif; ?>

            <button type="submit">🔍 Buscar</button>
        </div>
    </form>
</div>

<div id="botonQuitarFlotante" class="btn-reset-flotante oculto">
    <a href="picajeindex.php?view=historial" class="btn-reset">🔄 Quitar filtros</a>
</div>


<div class="table-container">
    <div class="table-wrapper">

        <!-- CABECERA DE LA TABLA -->
        <div class="table-header">
            <?php if ($user->admin == 1): ?>
                <div class="cell">Usuario</div>
            <?php endif; ?>
            <div class="cell">Tipo</div>
            <div class="cell">Hora</div>
            <div class="cell">Fecha</div>
            <div class="cell">Origen</div>
        </div>

        <!-- SI NO HAY DATOS -->
        <?php if (empty($historial)): ?>
            <div class="row-wrapper no-data">
                <div class="cell">No hay registros disponibles.</div>
            </div>

        <!-- MOSTRAR DATOS -->
        <?php else: ?>
            <?php foreach ($historial as $registro): ?>
                <div class="row-wrapper">

                    <?php if ($user->admin == 1): ?>
                        <div class="cell cell-usuario"><?php echo dol_escape_htmltag($registro['usuario']); ?></div>
                    <?php endif; ?>

                    <div class="cell"><?php echo dol_escape_htmltag($registro['tipo']); ?></div>
                    <div class="cell"><?php echo dol_escape_htmltag($registro['hora']); ?></div>
                    <div class="cell"><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></div>
                    <div class="cell"><?php echo dol_escape_htmltag($registro['tipo_registro'] ?? 'manual'); ?></div>

                    <div class="floating-buttons">
                        <button type="button" class="locButton tableButton" onclick="verUbicacion(<?php echo (int)$registro['id']; ?>)">📍</button>

                        <?php if ($user->admin == 1): ?>
                            <button type="button" class="editButton tableButton" onclick="abrirModalEditar(<?php echo (int)$registro['id']; ?>)">✏️</button>  
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

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

<!-- MODAL PARA EDITAR -->
<div id="modalEditar" class="modal-overlay" style="display: none;">
    <div class="modal-content" id="modalEditarContenido"></div>
</div>

<!-- MODAL PARA VER UBICACIÓN -->
<div id="modalUbicacion" class="modal-overlay" style="display: none;">
    <div class="modal-content" id="modalUbicacionContenido"></div>
</div>

<script>
    const URL_GET_UBICACION = '<?php echo dol_buildpath("/custom/picaje/ajax/get_ubicacion.php", 1); ?>';
    const URL_GET_PICAJE = '<?php echo dol_buildpath("/custom/picaje/ajax/get_picaje.php", 1); ?>';
    const URL_MODIFICAR_PICAJE = '<?php echo dol_buildpath("/custom/picaje/ajax/modificar_picaje.php", 1); ?>';
    const URL_LOG_MODIFICACIONES = '<?php echo dol_buildpath('/custom/picaje/picajeindex.php?view=log_modificaciones', 1); ?>';
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
        }
    });
</script>

