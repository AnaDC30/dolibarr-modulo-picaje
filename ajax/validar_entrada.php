<?php
require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = (int) $user->id;

$entrada_anticipada = false;

// Verificar si aún no ha picado entrada
$estado = getEstadoPicajeUsuario($user_id);
if (!$estado['entrada']) {
    $horario = getHorarioUsuario($user_id);
    $hora_teorica = $horario->hora_entrada ?: getHoraEntradaEmpresaPorDefecto();

    $ahora = strtotime(date('H:i:s'));
    $hora_limite = strtotime($hora_teorica);

    if ($ahora < $hora_limite) {
        $entrada_anticipada = true;
    }
}

echo json_encode([
    'entrada_anticipada' => $entrada_anticipada,
    'anticipada' => $entrada_anticipada // alias para frontend
]);
exit;

