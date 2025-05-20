<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php'; 

// Protección de Acceso - Permisos del Módulo
global $user;

if (empty($user->rights->picaje->editar)) { 
    http_response_code(403);
    exit('Acceso denegado.');
}

if (empty($user->rights->picaje->ver)) {
    http_response_code(403);
    exit('Acceso denegado.');
}


// Validación del parámetro ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Error: ID inválido.</p>";
    exit;
}

$id = (int) $_GET['id'];
$registro = obtenerPicajePorId($id);

if (!$registro) {
    echo "<p>Error: Registro no encontrado.</p>";
    exit;
}

// Extraer fecha y hora desde campo fecha_hora
$fecha = '';
$hora = '';
if (!empty($registro['fecha_hora'])) {
    $fecha = date('Y-m-d', strtotime($registro['fecha_hora']));
    $hora = date('H:i', strtotime($registro['fecha_hora']));
}

?>

<!-- ===================== -->
<!--  MODAL EDITAR PICAJE  -->
<!-- ===================== -->

<h2 class="titre">Editar Registro de Picaje</h2>

<form onsubmit="return guardarEdicion(event)">
    <input type="hidden" name="id_picaje" value="<?php echo $registro['id']; ?>">

    <div class="formelement">
        <label for="tipo">Tipo de Picaje:</label><br>
        <select name="tipo" id="tipo" class="flat ui-widget ui-corner-all">
            <option value="entrada" <?php if ($registro['tipo'] === 'entrada') echo 'selected'; ?>>Entrada</option>
            <option value="salida" <?php if ($registro['tipo'] === 'salida') echo 'selected'; ?>>Salida</option>
        </select>
    </div>

    <div class="formelement">
        <label for="hora">Hora:</label><br>
        <input type="time" name="hora" id="hora" value="<?php echo $hora; ?>" required class="flat ui-widget ui-corner-all">
    </div>

    <div class="formelement">
        <label for="fecha">Fecha:</label><br>
        <input type="date" name="fecha" id="fecha" value="<?php echo $fecha; ?>" required class="flat ui-widget ui-corner-all">
    </div>

    <div class="formelement">
        <label for="comentario">Comentario (obligatorio):</label><br>
        <textarea name="comentario" id="comentario" rows="4" required class="flat ui-widget ui-corner-all" style="width: 100%;"></textarea>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all">💾 Guardar Cambios</button>
        <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all" onclick="cerrarModalEditar()">Cancelar</button>
    </div>
</form>

