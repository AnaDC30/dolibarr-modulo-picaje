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

// =====================
//    CABECERA VISUAL
// =====================
llxHeader("", "Historial de Picaje", "");

// =====================
//   OBTENER DATOS BBDD
// =====================
$historial = obtenerHistorialPicajes();
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
<div class="table-container">
    <div class="table-wrapper">

        <!-- CABECERA DE LA TABLA -->
        <div class="table-header">
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
                    <div class="cell"><?php echo $registro['tipo']; ?></div>
                    <div class="cell"><?php echo $registro['hora']; ?></div>
                    <div class="cell"><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></div>

                    <!-- BOTONES DE ACCIÓN SI ES ADMIN -->
                    <?php if ($user->admin == 1): ?>
                        <div class="floating-buttons">
                            <form method="post" action="../core/modules/modificar_picaje.php">
                                <input type="hidden" name="id_picaje" value="<?php echo $registro['id']; ?>">
                                <button type="submit" name="accion" value="editar" class="editButton tableButton">✏️</button>
                            </form>
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

<?php
// =====================
//     PIE DE PÁGINA
// =====================
llxFooter();
?>