<?php

define('NOREQUIREMENU', 1);
define('NOREQUIREHTML', 1);
define('NOTOKENRENEWAL', 1);
define('NOCSRFCHECK', 1);

require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = (int) $user->id;

$salida_anticipada = false;

$estado = getEstadoPicajeUsuario($user_id);
if ($estado['entrada'] && !$estado['salida']) {
    $horario = getHorarioUsuario($user_id);
    $hora_teorica = $horario->hora_salida ?: getHoraSalidaEmpresaPorDefecto();

    $ahora = strtotime(date('H:i:s'));
    $hora_limite = strtotime($hora_teorica);

    if ($ahora < $hora_limite) {
        $salida_anticipada = true;
    }
}

echo json_encode([
    'salida_anticipada' => $salida_anticipada
]);
exit;
