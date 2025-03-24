<?php
// =====================
//  ENTORNO DE DOLIBARR
// =====================
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';


// =====================
//  CARGAR ESTILOS CSS
// =====================
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/custom/mimodulo/css/historial.css">';
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/custom/mimodulo/css/modal.css">';

// =====================
//    CABECERA VISUAL
// =====================
llxHeader("", "Historial de Picaje", "");

// =====================
//   OBTENER DATOS BBDD
// =====================
$filtroFecha = $_GET['fecha'] ?? null;
$filtroUsuario = $_GET['usuario'] ?? null;
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
            <input type="date" name="fecha" id="fecha" value="<?php echo $_GET['fecha'] ?? ''; ?>">

            <?php if ($user->admin == 1): ?>
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" placeholder="Nombre o apellido" value="<?php echo $_GET['usuario'] ?? ''; ?>">
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
        </div>

        <!-- SI NO HAY DATOS -->
        <?php if (empty($historial)): ?>
            <div class="row-wrapper no-data">
                <div class="cell" colspan="3">No hay registros disponibles.</div>
            </div>

        <!-- MOSTRAR DATOS -->
        <?php else: ?>
            <?php foreach ($historial as $index => $registro): ?>
                <div class="row-wrapper">

                <?php if ($user->admin == 1): ?>
                    <div class="cell cell-usuario"><?php echo $registro['usuario']; ?></div>
                    <?php endif; ?>

                    <div class="cell"><?php echo $registro['tipo']; ?></div>
                    <div class="cell"><?php echo $registro['hora']; ?></div>
                    <div class="cell"><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></div>

                    <!-- BOTONES DE ACCIÓN SI ES ADMIN -->
                    <?php if ($user->admin == 1): ?>
                        <div class="floating-buttons">
                            <button type="button" class="locButton tableButton" onclick="verUbicacion(<?php echo $registro['id']; ?>)">📍</button>
                            <button type="button" class="editButton tableButton" onclick="abrirModalEditar(<?php echo $registro['id']; ?>)">✏️</button>  
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
    <a href="principal.php" class="backArrow">&#8592;</a>
</div>

<!-- ===================== -->
<!--    MODAL DE EDICIÓN   -->
<!-- ===================== -->

<div id="modalEditar" class="modal-overlay">
  <div id="modalEditarContenido" class="modal-content">
    <!-- Aquí se carga el formulario por AJAX -->
  </div>
</div>

<!-- ===================== -->
<!--  MODAL DE UBICACIÓN   -->
<!-- ===================== -->

<div id="modalUbicacion" class="modal-overlay">
  <div class="modal-content">
    <button class="cerrarModal" onclick="cerrarModalUbicacion()">×</button>
    <div id="modalUbicacionContenido">Cargando ubicación...</div>
  </div>
</div>


<!-- ===================== -->
<!--      SCRIPTS JS       -->
<!-- ===================== -->
<script src="<?php echo DOL_URL_ROOT; ?>/custom/mimodulo/scripts/modal-editar.js"></script>

<script>
    const DOLIBARR_CSRF_TOKEN = '<?php echo newToken(); ?>';
</script>

<script>
    function toggleFiltros() {
        const contenedor = document.getElementById("filtrosContainer");
        contenedor.classList.toggle("oculto");
    }
</script>



<?php
// =====================
//     PIE DE PÁGINA
// =====================
llxFooter();
?>