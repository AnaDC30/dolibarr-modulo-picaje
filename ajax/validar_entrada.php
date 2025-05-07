<?php
require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/autoentrada.php';

global $user, $conf;
header('Content-Type: application/json');

$data      = json_decode(file_get_contents('php://input'), true);
$user_id   = (int) $user->id;
$latitud   = isset($data['latitud']) ? floatval($data['latitud']) : null;
$longitud  = isset($data['longitud']) ? floatval($data['longitud']) : null;

$estado = getEstadoPicajeUsuario($user_id); // entrada/salida
$auto_entry = false;
$entrada_anticipada = false;

// Si NO ha picado entrada aún...
if (!$estado['entrada']) {
    // 1. Intentar entrada automática
    $auto_entry = ejecutarEntradaAutomaticaUsuario($user_id, $latitud, $longitud);

    // 2. Si no hay entrada, verificar si sería anticipada
    if (!$auto_entry) {
        $horario      = getHorarioUsuario($user_id);
        $hora_teorica = $horario->hora_entrada ?: getHoraEntradaEmpresaPorDefecto();
        $ahora        = strtotime(date('H:i:s'));
        $hora_limite  = strtotime($hora_teorica);

        if ($ahora < $hora_limite) {
            $entrada_anticipada = true;
        }
    }
}

echo json_encode([
    'auto_entry'         => (bool) $auto_entry,
    'entrada_anticipada' => (bool) $entrada_anticipada,
    'anticipada'         => (bool) $entrada_anticipada // alias para frontend
]);
exit;
