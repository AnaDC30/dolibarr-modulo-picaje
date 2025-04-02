<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

global $user;

header('Content-Type: application/json');

// Obtener horario del usuario
$horario = getHorarioUsuario($user->id);
$hora_actual = date('H:i:s');
$hora_salida = $horario->hora_salida ?? '14:00:00';

echo json_encode([
    'salida_anticipada' => strtotime($hora_actual) < strtotime($hora_salida)
]);
exit;
