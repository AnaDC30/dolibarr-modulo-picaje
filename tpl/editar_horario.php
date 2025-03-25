<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

$langs->load("admin");

if (!$user->admin) {
    accessforbidden();
}

$user_id = GETPOST('user_id', 'int');
if (!$user_id) {
    accessforbidden();
}

$page_name = "Editar horario de picaje";
llxHeader('', $page_name);

print load_fiche_titre("Editar horario para el usuario ID: $user_id", '', 'title_setup');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hora = GETPOST('hora_salida', 'alpha');
    $auto = GETPOST('salida_automatica', 'int');

    $sql_exist = "SELECT rowid FROM llx_picaje_horarios WHERE fk_user = $user_id AND entity = " . (int) $conf->entity;
    $res_exist = $db->query($sql_exist);

    if ($res_exist && $db->num_rows($res_exist)) {
        // Update
        $rowid = $db->fetch_object($res_exist)->rowid;
        $sql = "UPDATE llx_picaje_horarios SET hora_salida = '" . $db->escape($hora) . "', salida_automatica = $auto 
                WHERE rowid = $rowid";
    } else {
        // Insert
        $sql = "INSERT INTO llx_picaje_horarios (fk_user, hora_salida, salida_automatica, entity) 
                VALUES ($user_id, '" . $db->escape($hora) . "', $auto, " . (int) $conf->entity . ")";
    }

    if ($db->query($sql)) {
        setEventMessages("✔️ Horario actualizado", null, 'mesgs');
    } else {
        setEventMessages("❌ Error: " . $db->lasterror(), null, 'errors');
    }

    header("Location: " . dol_buildpath("/user/card.php?id=" . $user_id, 1));
    exit;
}
?>

<form method="post">
    <table class="noborder" width="50%">
        <tr class="liste_titre">
            <th colspan="2">Configuración de horario</th>
        </tr>
        <tr>
            <td>Hora de salida prevista:</td>
            <td><input type="time" name="hora_salida" value="14:00" required></td>
        </tr>
        <tr>
            <td>Salida automática:</td>
            <td><input type="checkbox" name="salida_automatica" value="1"></td>
        </tr>
    </table>

    <div class="center">
        <br><input type="submit" class="button" value="💾 Guardar horario">
    </div>
</form>

<?php
llxFooter();
?>
