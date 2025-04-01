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
//    CABECERA VISUAL
// =====================

// =====================
//   OBTENER DATOS BBDD
// =====================
$filtroFecha = GETPOST('fecha', 'alpha');
$filtroUsuario = GETPOST('usuario', 'alpha');
$historial = obtenerHistorialPicajes($filtroFecha, $filtroUsuario);

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

<!-- Contenedor del formulario oculto inicialmente -->
<div id="filtrosContainer" class="filtro-formulario oculto">
    <form method="GET">
        <div class="filtros">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" value="<?php echo dol_escape_htmltag($filtroFecha); ?>">

            <?php if ($user->admin == 1): ?>
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" placeholder="Nombre o apellido" value="<?php echo dol_escape_htmltag($filtroUsuario); ?>">
            <?php endif; ?>

            <button type="submit">🔍 Buscar</button>
            <a href="historial.php" class="btn-reset">Limpiar</a>
        </div>
    </form>
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
                <div class="cell" colspan="3">No hay registros disponibles.</div>
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

                    <!-- BOTONES DE ACCIÓN SI ES ADMIN -->
                    <?php if ($user->admin == 1): ?>
                        <div class="floating-buttons">
                            <button type="button" class="locButton tableButton" onclick="verUbicacion(<?php echo (int)$registro['id']; ?>)">📍</button>
                            <button type="button" class="editButton tableButton" onclick="abrirModalEditar(<?php echo (int)$registro['id']; ?>)">✏️</button>  
                        </div>
                    <?php endif; ?>
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




<!-- Scripts JS -->
<script src="<?php echo dol_buildpath('/custom/picaje/js/picaje.js', 1); ?>"></script>

<script>
    const DOLIBARR_CSRF_TOKEN = '<?php echo newToken(); ?>';

    function toggleFiltros() {
        const contenedor = document.getElementById("filtrosContainer");
        contenedor.classList.toggle("oculto");
    }
</script>

