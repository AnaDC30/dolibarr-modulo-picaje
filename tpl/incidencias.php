<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

$langs->load("admin");

if (!$user->admin || $user->id != 1) {
    accessforbidden();
}

echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/incidencias.css', 1) . '">';

print load_fiche_titre("📋 Gestión de incidencias de picaje");

global $db, $conf;

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
    print '<div class="main-content" style="margin:auto;max-width:900px">';
    print '<h3>Incidencias registradas</h3>';
    print '<table class="liste">';
    print '<tr><th>Usuario</th><th>Fecha</th><th>Hora</th><th>Tipo</th><th>Motivo</th><th>Acción</th></tr>';

    while ($obj = $db->fetch_object($res)) {
        $nombre = dol_escape_htmltag($obj->firstname . ' ' . $obj->lastname);
        $tipo = $obj->tipo === 'horas_extra' ? 'Horas extra' : 'Salida anticipada';
        $fecha = dol_print_date(dol_stringtotime($obj->fecha), 'day');
        $hora = dol_print_date(dol_stringtotime($obj->hora), 'hourminute');

        print '<tr>';
        print "<td>$nombre</td>";
        print "<td>$fecha</td>";
        print "<td>$hora</td>";
        print "<td>$tipo</td>";
        print "<td>" . dol_escape_htmltag($obj->justificacion) . "</td>";
        print '<td><a class="btn-historial" href="' . dol_buildpath('/custom/picaje/index.php?view=historial&user_id=' . $obj->fk_user, 1) . '">Ver historial</a></td>';
        print '</tr>';
    }

    print '</table>';
    print '</div>';
} else {
    print '<p class="center">No hay incidencias registradas.</p>';
}

?>

<!-- =================== -->
<!--    BOTÓN VOLVER     -->
<!-- =================== -->

<div class="backContainer">
    <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php', 1); ?>" class="backArrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-big-left-icon lucide-arrow-big-left">
            <path d="M18 15h-6v4l-7-7 7-7v4h6v6z"/>
        </svg>
    </a>
</div>
