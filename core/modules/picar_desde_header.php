<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

header('Content-Type: application/json');

global $db, $user, $conf;

// Validar sesión activa
if (empty($user->id)) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado.']);
    exit;
}

// Validar ubicación
$lat = GETPOST('latitud');
$lon = GETPOST('longitud');

if (!$lat || !$lon) {
    echo json_encode(['success' => false, 'error' => 'Ubicación no recibida.']);
    exit;
}

// Obtener estado actual de picaje
$estado = getEstadoPicajeUsuario($user->id);
$fecha = date('Y-m-d');
$hora = date('H:i:s');
$tipo = '';
$justificacion = null;
$salida_manual = 0;

// Determinar tipo de picaje
if (!$estado['entrada']) {
    $tipo = 'entrada';
} elseif ($estado['entrada'] && !$estado['salida']) {
    $tipo = 'salida';

    // Validar si es salida anticipada
    $horario = getHorarioUsuario($user->id);
    if (strtotime($hora) < strtotime($horario->hora_salida)) {
        $salida_manual = 1;
        $justificacion = 'Salida anticipada sin justificar (desde header)';
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Ya has completado tu jornada de hoy.'
    ]);
    exit;
}

// Guardar picaje
$sql = "INSERT INTO llx_picaje (fecha, hora, tipo, usuario_id, latitud, longitud, salida_manual, justificacion)
        VALUES (
            '" . $db->escape($fecha) . "',
            '" . $db->escape($hora) . "',
            '" . $db->escape($tipo) . "',
            " . (int) $user->id . ",
            '" . $db->escape($lat) . "',
            '" . $db->escape($lon) . "',
            $salida_manual,
            " . ($justificacion ? "'" . $db->escape($justificacion) . "'" : "NULL") . "
        )";

$res = $db->query($sql);

if ($res) {
    // Nuevo estado
    $nuevo_estado = getEstadoPicajeUsuario($user->id);

    echo json_encode([
        'success' => true,
        'tipo' => $tipo,
        'mensaje' => "✅ Picaje de $tipo registrado correctamente.",
        'estado' => $nuevo_estado
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Error al registrar picaje: ' . $db->lasterror()
    ]);
}
exit;
