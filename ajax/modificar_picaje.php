<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

header('Content-Type: application/json');

global $user, $db;

// Verificación de sesión válida
if (!$user || empty($user->id)) {
    echo json_encode(['success' => false, 'error' => 'Sesión no válida o expirada.']);
    exit;
}

// Verificación de permisos
if ($user->admin != 1) {
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado.']);
    exit;
}

// Validación de datos obligatorios
if (
    !isset($_POST['id_picaje']) ||
    !isset($_POST['tipo']) ||
    !isset($_POST['hora']) ||
    !isset($_POST['fecha']) ||
    empty(trim($_POST['comentario']))
) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos o comentario obligatorio.']);
    exit;
}

$id = (int) $_POST['id_picaje'];
$tipoNuevo = trim($_POST['tipo']);
$horaNueva = trim($_POST['hora']);
$fechaNueva = trim($_POST['fecha']);
$comentario = trim($_POST['comentario']);
$usuarioId = $user->id;

// Combinar fecha y hora
$fechaHoraNueva = $fechaNueva . ' ' . $horaNueva . ':00';

// Obtener registro actual
$registroActual = obtenerPicajePorId($id);
if (!$registroActual) {
    echo json_encode(['success' => false, 'error' => 'Registro no encontrado.']);
    exit;
}

// Comparar cambios
$descripcion = [];

if ($registroActual['tipo'] !== $tipoNuevo) {
    $descripcion[] = "Se cambió el tipo de '{$registroActual['tipo']}' a '$tipoNuevo'";
}

$fechaHoraActual = $registroActual['fecha_hora'];  
$fechaHoraNueva = $fechaNueva . ' ' . $horaNueva;

if ($fechaHoraActual !== $fechaHoraNueva) {
    $descripcion[] = "Se cambió la fecha/hora de $fechaHoraActual a $fechaHoraNueva";
}



$descripcionTexto = implode('. ', $descripcion);

// Actualizar registro
$sqlUpdate = "UPDATE llx_picaje
              SET tipo = '" . $db->escape($tipoNuevo) . "',
                  fecha_hora = '" . $db->escape($fechaHoraNueva) . "'
              WHERE id = $id";

$resql = $db->query($sqlUpdate);
if (!$resql) {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar el registro.']);
    exit;
}

// Insertar en el log de cambios
$sqlLog = "INSERT INTO llx_modificacion_picaje (picaje_id, usuario_id, descripcion, comentario)
           VALUES ($id, $usuarioId, '" . $db->escape($descripcionTexto) . "', '" . $db->escape($comentario) . "')";
$db->query($sqlLog);

// Éxito
echo json_encode(['success' => true]);
exit;

