<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/custom/mimodulo/css/log.css">';

llxHeader("", "Historial de Modificaciones de Picaje", "");

// Obtener logs desde la base de datos
global $db;
$sql = "SELECT l.*, u.login AS usuario 
        FROM llx_mimodulo_picaje_log l
        LEFT JOIN ".MAIN_DB_PREFIX."user u ON u.rowid = l.usuario_id
        ORDER BY l.fecha_modificacion DESC";
$resql = $db->query($sql);
?>

<header class="page-header">
    <h1>Historial de Modificaciones</h1>
</header>

<div class="log-container">
    <table class="log-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Picaje</th>
                <th>Usuario</th>
                <th>Descripción</th>
                <th>Comentario</th>
                <th>Fecha Modificación</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resql && $db->num_rows($resql) > 0): ?>
                <?php while ($row = $db->fetch_array($resql)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['picaje_id']; ?></td>
                        <td><?php echo $row['usuario']; ?></td>
                        <td><?php echo $row['descripcion']; ?></td>
                        <td><?php echo $row['comentario']; ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($row['fecha_modificacion'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No hay modificaciones registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="backContainer">
    <a href="historial.php" class="backArrow">&#8592;</a>
</div>

<?php
llxFooter();
?>
