<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

//Proteccion de Acceso
global $user;
if ($user->admin != 1) {
    http_response_code(403);
    exit('Acceso denegado.');
}


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
?>

<div class="modal-inner-form">
    <button class="cerrarModal" onclick="cerrarModalEditar()">×</button>
    <h2>Editar Registro de Picaje</h2>

    <form onsubmit="return guardarEdicion(event)">
        <input type="hidden" name="id_picaje" value="<?php echo $registro['id']; ?>">

        <label for="tipo">Tipo de Picaje:</label>
        <select name="tipo" id="tipo">
            <option value="entrada" <?php if ($registro['tipo'] === 'entrada') echo 'selected'; ?>>Entrada</option>
            <option value="salida" <?php if ($registro['tipo'] === 'salida') echo 'selected'; ?>>Salida</option>
        </select>

        <label for="hora">Hora:</label>
        <input type="time" name="hora" id="hora" value="<?php echo $registro['hora']; ?>" required>

        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" id="fecha" value="<?php echo $registro['fecha']; ?>" required>

        <label for="comentario">Comentario (obligatorio):</label>
        <textarea name="comentario" id="comentario" rows="4" required></textarea>

        <div class="modal-actions">
            <button type="submit" class="guardarButton">Guardar Cambios</button>
            <button type="button" onclick="cerrarModalEditar()">Cancelar</button>
        </div>
    </form>
</div>

