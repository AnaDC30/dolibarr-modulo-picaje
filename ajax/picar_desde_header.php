<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/dolibarr/main.inc.php';

require_once DOL_DOCUMENT_ROOT . '/custom/picaje/class/picaje.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

header('Content-Type: application/json');

global $db, $user;

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
$fecha = dol_print_date(dol_now(), '%Y-%m-%d');
$hora = dol_print_date(dol_now(), '%H:%M:%S');

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

// Guardar picaje usando la clase
$picaje = new Picaje($db);
$picaje->fk_user = $user->id;
$picaje->tipo = $tipo;
$picaje->latitud = $lat;
$picaje->longitud = $lon;
$picaje->fecha_hora = dol_now();
$picaje->salida_manual = $salida_manual;
$picaje->justificacion = $justificacion;
$picaje->tipo_registro = 'desde_header';

if ($picaje->create($user) > 0) {
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
