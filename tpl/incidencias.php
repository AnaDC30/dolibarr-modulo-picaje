<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

$langs->load("admin");

if (!$user->admin || $user->id != 1) {
    accessforbidden();
}

echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/style.css.php">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/layout.css', 1) . '">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/modal.css', 1) . '">';

global $db, $conf;
?>

<!-- ===================== -->
<!--       ENCABEZADO      -->
<!-- ===================== -->
<div class="titre">
    <span class="inline-block valignmiddle">
        <?php echo img_picto('', 'picaje@picaje'); ?>
    </span>
    <span class="inline-block valignmiddle" style="font-size: 22px; font-weight: bold;">
        <?php echo $langs->trans("Incidencias"); ?>
    </span>
</div>

<?php

// =======================
// CONSULTA DE INCIDENCIAS
// =======================
$sql = "SELECT i.*, u.firstname, u.lastname 
        FROM llx_picaje_incidencias i
        LEFT JOIN llx_user u ON u.rowid = i.fk_user
        WHERE i.entity = " . (int) $conf->entity . "
        ORDER BY i.fecha DESC, i.hora DESC";

$res = $db->query($sql);

if ($res && $db->num_rows($res)) {
    echo '<div class="div-table-responsive" style="margin-top: 20px;">';
    echo '<table class="noborder allwidth">';
    echo '<thead class="liste_titre">';
    echo '<tr><th>Usuario</th><th>Fecha</th><th>Hora</th><th>Tipo</th><th>Motivo</th><th>Estado</th><th>Resolución</th><th>Acción</th></tr>';
    echo '</thead><tbody>';

    while ($obj = $db->fetch_object($res)) {
        $nombre = dol_escape_htmltag($obj->firstname . ' ' . $obj->lastname);
        $fecha = dol_print_date(dol_stringtotime($obj->fecha), 'day');
        $hora = substr($obj->hora, 0, 5);

        // Tipo legible
        switch ($obj->tipo) {
            case 'horas_extra': $tipo = 'Horas extra'; break;
            case 'salida_anticipada': $tipo = 'Salida anticipada'; break;
            case 'entrada_anticipada': $tipo = 'Entrada anticipada'; break;
            case 'olvido_picaje': $tipo = 'Olvido de picaje'; break;
            case 'otro': $tipo = 'Otro'; break;
            default: $tipo = ucfirst($obj->tipo); break;
        }

        $estado = dol_escape_htmltag($obj->status);
        $estadoClase = strtolower($estado); // pendiente o resuelta
        $resolucion = !empty($obj->resolucion) ? dol_escape_htmltag($obj->resolucion) : '-';
        $urlHistorial = dol_buildpath('/custom/picaje/picajeindex.php', 1) . '?view=historial&user_id=' . $obj->fk_user . '&desde=incidencias';

        echo '<tr class="oddeven">';
        echo "<td class=\"center\">$nombre</td>";
        echo "<td class=\"center\">$fecha</td>";
        echo "<td class=\"center\">$hora</td>";
        echo "<td class=\"center\">$tipo</td>";
        echo "<td class=\"center\">" . dol_escape_htmltag($obj->comentario) . "</td>";

        // Estado editable
        echo '<td class="center">';
        if ($user->admin == 1) {
            echo '<button class="btn-status status-btn ' . $estadoClase . '" data-id="' . $obj->rowid . '" data-status="' . $estado . '">' . $estado . '</button>';
        } else {
            echo '<span class="status-btn ' . $estadoClase . '">' . $estado . '</span>';
        }
        echo '</td>';

        echo "<td class=\"center\">$resolucion</td>";

        // Acciones
        echo '<td class="center">';
        echo '<a class="btn-historial-incidencias" href="' . $urlHistorial . '">Ver historial</a>';
        if ($estadoClase === 'pendiente') {
            echo '<button class="btn-crear-incidencias" onclick="abrirModalCrearPicaje(' . (int)$obj->rowid . ')">⚠️ Crear picaje</button>';
        }
        echo '</td>';

        echo '</tr>';
    }

    echo '</tbody></table></div>';
}
?>


<!-- MODAL PARA CREAR PICAJE -->
<?php include_once dol_buildpath('/custom/picaje/tpl/modales.php', 0); ?>

<script src="<?php echo dol_buildpath('/custom/picaje/js/picaje.js', 1); ?>"></script>
