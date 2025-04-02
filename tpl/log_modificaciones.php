<?php
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once dol_buildpath('/custom/picaje/lib/dbController.php', 0);

// Cargar CSS
print '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/log.css', 1) . '">';


// Obtener logs desde la base de datos
global $db;
$sql = "SELECT l.id, l.descripcion, l.comentario, l.fecha_modificacion,
               CONCAT(u.firstname, ' ', u.lastname) AS nombre_usuario_afectado
        FROM " . MAIN_DB_PREFIX . "modificacion_picaje l
        LEFT JOIN " . MAIN_DB_PREFIX . "picaje p ON p.id = l.picaje_id
        LEFT JOIN " . MAIN_DB_PREFIX . "user u ON u.rowid = p.fk_user
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
                <th>Usuario Afectado</th>
                <th>Descripción</th>
                <th>Comentario</th>
                <th>Fecha de Modificación</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resql && $db->num_rows($resql) > 0): ?>
                <?php while ($row = $db->fetch_array($resql)): ?>
                    <tr>
                        <td><?php echo (int)$row['id']; ?></td>
                        <td><?php echo dol_escape_htmltag($row['nombre_usuario_afectado'] ?? '—'); ?></td>
                        <td><?php echo dol_escape_htmltag($row['descripcion']); ?></td>
                        <td><?php echo dol_escape_htmltag($row['comentario']); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($row['fecha_modificacion'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No hay modificaciones registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
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
