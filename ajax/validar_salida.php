<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

global $user, $conf;

header('Content-Type: application/json');


// ===============================
// CONFIGURACIÓN DEL MÓDULO
// ===============================
$salidaAutomaticaActiva = getDolGlobalInt('PICAR_SALIDA_AUTOMATICA');
$modoHorario = getDolGlobalString('PICAR_MODO_HORARIO', 'usuario'); // 'usuario' o 'departamento'

// ===============================
// CONTROL DE LÓGICA DE SALIDA
// ===============================
$hora_actual = date('H:i:s');

// Si no hay lógica de salida automática activa, se permite siempre picar
if (!$salidaAutomaticaActiva) {
    echo json_encode(['salida_anticipada' => false]);
    exit;
}

// ===============================
// OBTENER HORA DE SALIDA SEGÚN CONFIGURACIÓN
// ===============================

$modoHorario = getDolGlobalString('PICAR_MODO_HORARIO') ?: 'usuario'; // usuario, departamento, etc.
$hora_salida = null;

if ($modoHorario === 'usuario') {
    $horario = getHorarioUsuario($user->id);
    if ($horario && !empty($horario->hora_salida)) {
        $hora_salida = $horario->hora_salida;
    }
} elseif ($modoHorario === 'departamento') {
    $horario = getHorarioDepartamento($user->fk_departement);
    if ($horario && !empty($horario->hora_salida)) {
        $hora_salida = $horario->hora_salida;
    }
}

// Si no hay horario personalizado, usar el de empresa
if (empty($hora_salida)) {
    $hora_salida = getHoraSalidaEmpresaPorDefecto(); // 👈 función definida en dbController
}


// ===============================
// COMPARACIÓN DE HORAS
// ===============================
$esAnticipada = strtotime($hora_actual) < strtotime($hora_salida);

echo json_encode(['salida_anticipada' => $esAnticipada]);
exit;
