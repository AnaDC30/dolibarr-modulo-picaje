<?php
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

$langs->load("admin");

if (!$user->admin) {
    accessforbidden();
}

$grupo_id = GETPOST('grupo_id', 'int');
if (!$grupo_id) {
    accessforbidden();
}

$page_name = "Editar horario de grupo";

print load_fiche_titre("Editar horario del grupo ID: $grupo_id", '', 'title_setup');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hora = GETPOST('hora_salida', 'alpha');
    $auto = GETPOST('salida_automatica', 'int');

    $sql_exist = "SELECT rowid FROM " . MAIN_DB_PREFIX . "picaje_horarios WHERE fk_departement = " . (int)$grupo_id . " AND entity = " . (int)$conf->entity;
    $res_exist = $db->query($sql_exist);

    if ($res_exist && $db->num_rows($res_exist)) {
        $rowid = $db->fetch_object($res_exist)->rowid;
        $sql = "UPDATE " . MAIN_DB_PREFIX . "picaje_horarios SET hora_salida = '" . $db->escape($hora) . "', salida_automatica = " . (int)$auto . " WHERE rowid = " . (int)$rowid;
    } else {
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "picaje_horarios (fk_departement, hora_salida, salida_automatica, entity) VALUES (" . (int)$grupo_id . ", '" . $db->escape($hora) . "', " . (int)$auto . ", " . (int)$conf->entity . ")";
    }

    if ($db->query($sql)) {
        setEventMessages("✔️ Horario actualizado", null, 'mesgs');
    } else {
        setEventMessages("❌ Error: " . $db->lasterror(), null, 'errors');
    }

    header("Location: " . dol_buildpath("/user/group/card.php?id=" . $grupo_id, 1));
    exit;
}

// Obtener valores actuales para mostrar en formulario
$sql_actual = "SELECT hora_salida, salida_automatica FROM " . MAIN_DB_PREFIX . "picaje_horarios WHERE fk_departement = " . (int)$grupo_id . " AND entity = " . (int)$conf->entity;
$res_actual = $db->query($sql_actual);

$hora_actual = '14:00';
$auto_actual = 0;

if ($res_actual && $db->num_rows($res_actual)) {
    $obj = $db->fetch_object($res_actual);
    $hora_actual = $obj->hora_salida;
    $auto_actual = $obj->salida_automatica;
}
?>

<form method="post">
    <table class="noborder" width="50%">
        <tr class="liste_titre">
            <th colspan="2">Configuración de horario</th>
        </tr>
        <tr>
            <td>Hora de salida prevista:</td>
            <td><input type="time" name="hora_salida" value="<?php echo dol_escape_htmltag($hora_actual); ?>" required></td>
        </tr>
        <tr>
            <td>Salida automática:</td>
            <td><input type="checkbox" name="salida_automatica" value="1" <?php echo ($auto_actual ? 'checked' : ''); ?>></td>
        </tr>
    </table>

    <div class="center">
        <br><input type="submit" class="button" value="💾 Guardar horario de grupo">
    </div>
</form>

