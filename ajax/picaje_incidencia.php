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
$sql = "SELECT i.rowid AS incidencia_id, i.fecha, i.hora, i.tipo, i.justificacion, 
               u.rowid AS user_id, CONCAT(u.firstname, ' ', u.lastname) AS usuario
        FROM llx_picaje_incidencias i
        JOIN llx_user u ON u.rowid = i.fk_user
        WHERE i.status = 'pendiente'";

$res = $db->query($sql);

$opciones = '';
if ($res && $db->num_rows($res) > 0) {
    while ($obj = $db->fetch_object($res)) {
        $tipo = ucfirst(str_replace('_', ' ', $obj->tipo));
        $justificacion = dol_trunc(dol_escape_htmltag($obj->justificacion), 30);
        $usuario = dol_escape_htmltag($obj->usuario);
        $fechaISO = $obj->fecha; // Lo pasamos directo, sin date()
        $fechaLegible = date('d/m/Y', strtotime($obj->fecha)); // Solo para mostrar en el select
        $hora = substr($obj->hora, 0, 5);

        $opciones .= "<option value='{$obj->incidencia_id}' 
            data-user='{$obj->user_id}' 
            data-fecha='{$fechaISO}'   
            data-hora='{$hora}'>
            [{$obj->incidencia_id}] {$fechaLegible} - {$usuario} - {$tipo}: {$justificacion}
        </option>";
    }
} else {
    echo "<p>⚠️ No hay incidencias pendientes.</p>";
    exit;
}


?>

<div class="modal-inner-form">
    <button class="cerrarModal" onclick="cerrarModalCrearPicaje()">×</button>
    <h2>Crear Picaje desde Incidencia</h2>

    <form id="picaje_incidencia">
        <label>Incidencia:</label>
        <select name="incidencia" id="incidencia">
            <option value="">--Selecciona--</option>
            <?php echo $opciones; ?>
        </select>

        <input type="hidden" name="fk_user" id="fk_user">

        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="entrada">Entrada</option>
            <option value="salida">Salida</option>
        </select>

        <label>Fecha:</label>
        <input type="date" name="fecha" id="fecha" required>

        <label>Hora:</label>
        <input type="time" name="hora" id="hora" required>

        <label>Comentario:</label>
        <textarea name="comentario" required></textarea>

        <div class="modal-actions">
            <button type="submit" class="guardarButton">Guardar Picaje</button>
            <button type="button" onclick="cerrarModalCrearPicaje()">Cancelar</button>
        </div>
    </form>
</div>
