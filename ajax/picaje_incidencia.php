<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/picaje/lib/dbController.php';
header('Content-Type: text/html; charset=UTF-8');

global $user, $db;

if ($user->admin != 1) {
    echo "<p>❌ Acceso no autorizado.</p>";
    exit;
}

// Obtener incidencias activas con usuario
$sql = "SELECT i.rowid AS incidencia_id, i.fecha, i.hora, i.tipo, i.comentario, 
               u.rowid AS user_id, CONCAT(u.firstname, ' ', u.lastname) AS usuario
        FROM llx_picaje_incidencias i
        JOIN llx_user u ON u.rowid = i.fk_user
        WHERE i.status = 'pendiente'";

$res = $db->query($sql);

$opciones = '';
if ($res && $db->num_rows($res) > 0) {
    while ($obj = $db->fetch_object($res)) {
        $tipo = ucfirst(str_replace('_', ' ', $obj->tipo));
        $comentario = dol_trunc(dol_escape_htmltag($obj->comentario), 30);
        $usuario = dol_escape_htmltag($obj->usuario);
        $fechaISO = $obj->fecha; 
        $fechaLegible = date('d/m/Y', strtotime($obj->fecha)); 
        $hora = substr($obj->hora, 0, 5);

        $opciones .= "<option value='{$obj->incidencia_id}' 
            data-user='{$obj->user_id}' 
            data-fecha='{$fechaISO}'   
            data-hora='{$hora}'>
            [{$obj->incidencia_id}] {$fechaLegible} - {$usuario} - {$tipo}: {$comentario}
        </option>";
    }
} else {
    echo "<p>⚠️ No hay incidencias pendientes.</p>";
    exit;
}


?>

<!-- ===================== -->
<!--  MODAL CREAR PICAJE   -->
<!-- ===================== -->

    <h2 class="titre">Crear Picaje desde Incidencia</h2>

    <form id="picaje_incidencia">
        <div class="formelement">
            <label for="incidencia">Incidencia:</label><br>
            <select name="incidencia" id="incidencia" class="flat ui-widget ui-corner-all" required>
                <option value="">--Selecciona--</option>
                <?php echo $opciones; ?>
            </select>
        </div>

        <input type="hidden" name="fk_user" id="fk_user">

        <div class="formelement">
            <label for="tipo">Tipo:</label><br>
            <select name="tipo" id="tipo" class="flat ui-widget ui-corner-all" required>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
            </select>
        </div>

        <div class="formelement">
            <label for="fecha">Fecha:</label><br>
            <input type="date" name="fecha" id="fecha" class="flat ui-widget ui-corner-all" required>
        </div>

        <div class="formelement">
            <label for="hora">Hora:</label><br>
            <input type="time" name="hora" id="hora" class="flat ui-widget ui-corner-all" required>
        </div>

        <div class="formelement">
            <label for="comentario">Comentario:</label><br>
            <textarea name="comentario" rows="4" class="flat ui-widget ui-corner-all" required style="width: 100%;"></textarea>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all">💾 Guardar Picaje</button>
            <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all" onclick="cerrarModalCrearPicaje()">Cancelar</button>
        </div>
    </form>



