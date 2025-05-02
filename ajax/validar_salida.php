<?php
// === Ajax wrapper para validar y ejecutar auto-salida ===

// Forzamos entorno para evitar bloqueos
$_SERVER['REQUEST_METHOD']  = 'GET';
$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF']        = '/dolibarr/custom/picaje/ajax/validar_salida.php';

// 1) Carga Dolibarr
require_once dirname(__DIR__, 3) . '/main.inc.php';

// 2) Incluimos controladores y lógica de negocio
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/autosalida.php';

global $user, $conf;
header('Content-Type: application/json');

// 3) Intentamos auto-salida
$data     = json_decode(file_get_contents('php://input'), true);
$user_id   = (int)$user->id;
$latitud  = isset($data['latitud'])  ? floatval($data['latitud'])  : null;
$longitud = isset($data['longitud']) ? floatval($data['longitud']) : null;
$auto_exit = ejecutarSalidaAutomaticaUsuario($user_id, $latitud, $longitud);

// 4) Si no hubo auto-salida, comprobamos si toca justificación
$salida_anticipada = false;
if (!$auto_exit) {
    $estado      = getEstadoPicajeUsuario($user_id);
    $horarioObj  = getHorarioUsuario($user_id);
    $hora_salida = $horarioObj->hora_salida ?: getHoraSalidaEmpresaPorDefecto();

    if (
        $estado['entrada'] &&
        !$estado['salida'] &&
        strtotime(date('H:i:s')) >= strtotime($hora_salida)
    ) {
        $salida_anticipada = true;
    }
}

// 5) Devolvemos JSON al frontend
echo json_encode([
    'auto_exit'         => (bool)$auto_exit,
    'salida_anticipada' => (bool)$salida_anticipada,
]);
exit;
