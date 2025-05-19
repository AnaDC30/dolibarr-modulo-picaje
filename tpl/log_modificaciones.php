<?php
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once dol_buildpath('/custom/picaje/lib/dbController.php', 0);

// Cargar CSS
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/style.css.php">';
print '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/layout.css', 1) . '">';


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

<div class="titre">
    <span class="inline-block valignmiddle">
        <?php echo img_picto('', 'picaje@picaje'); ?>
    </span>
    <span class="inline-block valignmiddle" style="font-size: 22px; font-weight: bold;">
        <?php echo $langs->trans("Historial de Modificaciones"); ?>
    </span>
</div>

<div class="div-table-responsive" style="margin-top: 20px;">
    <table class="noborder allwidth">
        <thead class="liste_titre">
            <tr>
                <th class="center">ID</th>
                <th class="center">Usuario Afectado</th>
                <th class="center">Descripción</th>
                <th class="center">Comentario</th>
                <th class="center">Fecha de Modificación</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resql && $db->num_rows($resql) > 0): ?>
                <?php while ($row = $db->fetch_array($resql)): ?>
                    <tr class="oddeven">
                        <td class="center"><?php echo (int)$row['id']; ?></td>
                        <td class="center"><?php echo dol_escape_htmltag($row['nombre_usuario_afectado'] ?? '—'); ?></td>
                        <td class="center"><?php echo dol_escape_htmltag($row['descripcion']); ?></td>
                        <td class="center"><?php echo dol_escape_htmltag($row['comentario']); ?></td>
                        <td class="center"><?php echo date("d/m/Y H:i", strtotime($row['fecha_modificacion'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr class="oddeven">
                    <td colspan="5" class="center opacitymedium">⚠️ No hay modificaciones registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

