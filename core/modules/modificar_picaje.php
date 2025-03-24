<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

header('Content-Type: application/json');

//Proteccion de Acceso
global $user;

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

// 1. Validación de datos obligatorios
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

// 2. Obtener datos actuales
$registroActual = obtenerPicajePorId($id);
if (!$registroActual) {
    echo json_encode(['success' => false, 'error' => 'Registro no encontrado.']);
    exit;
}

// 3. Comparar y construir descripción de cambios
$descripcion = [];
if ($registroActual['tipo'] !== $tipoNuevo) {
    $descripcion[] = "Se cambió el tipo de '{$registroActual['tipo']}' a '$tipoNuevo'";
}
if ($registroActual['hora'] !== $horaNueva) {
    $descripcion[] = "Se cambió la hora de {$registroActual['hora']} a $horaNueva";
}
if ($registroActual['fecha'] !== $fechaNueva) {
    $descripcion[] = "Se cambió la fecha de {$registroActual['fecha']} a $fechaNueva";
}

if (empty($descripcion)) {
    echo json_encode(['success' => false, 'error' => 'No se detectaron cambios.']);
    exit;
}

$descripcionTexto = implode('. ', $descripcion);

// 4. Actualizar el registro en la tabla principal
global $db;
$sqlUpdate = "UPDATE llx_picaje
              SET tipo = '" . $db->escape($tipoNuevo) . "',
                  hora = '" . $db->escape($horaNueva) . "',
                  fecha = '" . $db->escape($fechaNueva) . "'
              WHERE id = $id";
$resql = $db->query($sqlUpdate);

if (!$resql) {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar el registro.']);
    exit;
}

// 5. Insertar log de modificación
$sqlLog = "INSERT INTO llx_mimodulo_picaje_log (picaje_id, usuario_id, descripcion, comentario)
           VALUES ($id, $usuarioId, '" . $db->escape($descripcionTexto) . "', '" . $db->escape($comentario) . "')";
$resLog = $db->query($sqlLog);

// 6. Devolver respuesta
echo json_encode(['success' => true]);
exit;
