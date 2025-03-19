<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/includes/header.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

llxHeader("", "Historial de Picaje", "");

// Obtener historial desde la base de datos
$historial = obtenerHistorialPicajes();
?>

<header class="page-header">
    <h1>Historial de Picaje</h1>
</header>

    <table class="customTable">
        <tr>
            <th>Tipo</th>
            <th>Hora</th>
            <th>Fecha</th>
        </tr>

        <?php if (empty($historial)): ?>
            <tr>
                <td colspan="3">No hay registros disponibles.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($historial as $registro): ?>
                <tr>
                    <td><?php echo $registro['tipo']; ?></td>
                    <td><?php echo $registro['hora']; ?></td>
                    <td><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></td>
                    
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>


<div class="backContainer">
    <a href="principal.php" class="backArrow">&#8592;</a>
</div>

<?php
llxFooter();
?>


