<?php

define('NOTOKENRENEWAL', 1);
define('NOREQUIREMENU', 1);
define('NOREQUIREHTML', 1);
define('AJAX', 1);

require_once $_SERVER["DOCUMENT_ROOT"] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/class/picaje.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

header('Content-Type: application/json');

$response = ['success' => false];

// Verifica si el usuario está conectado
if (empty($user->id)) {
    $response['message'] = 'Usuario no autenticado';
    echo json_encode($response);
    exit;
}

// Recoger datos del POST
$tipo = GETPOST('tipo', 'alpha');
$lat = GETPOST('latitud', 'alpha');
$lng = GETPOST('longitud', 'alpha');

// Validar datos
if (!in_array($tipo, ['entrada', 'salida'])) {
    $response['message'] = 'Tipo no válido';
    echo json_encode($response);
    exit;
}

// Instanciar objeto picaje y guardar
$picaje = new Picaje($db);
$picaje->fk_user = $user->id;
$picaje->tipo = $tipo;
$picaje->latitud = $lat;
$picaje->longitud = $lng;
$picaje->fecha_hora = dol_now();
$picaje->salida_manual = 0; // por ahora asumimos 0 si no se controla
$picaje->justificacion = null;
$picaje->tipo_registro = 'manual'; // o 'desde_formulario'

/** 🔍 BLOQUE DE DEBUG **/
error_log("🧪 fk_user: " . $picaje->fk_user);
error_log("🧪 tipo: " . $picaje->tipo);
error_log("🧪 latitud: " . $picaje->latitud);
error_log("🧪 longitud: " . $picaje->longitud);
error_log("🧪 fecha_hora: " . $picaje->fecha_hora);
error_log("🧪 salida_manual: " . $picaje->salida_manual);
error_log("🧪 justificacion: " . $picaje->justificacion);
error_log("🧪 tipo_registro: " . $picaje->tipo_registro);
/** ------------------- **/

if ($picaje->create($user) > 0) {
    $response['success'] = true;
    $response['message'] = 'Picaje registrado correctamente';
} else {
    $response['message'] = 'Error al registrar picaje: ' . $db->lasterror();
}

echo json_encode($response);
exit;
