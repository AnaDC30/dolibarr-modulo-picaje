<?php
// =====================
//  ENTORNO DE DOLIBARR
// =====================
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
session_start();

global $db, $user, $conf;

// ==========================
//  VALIDACIÓN DE TOKEN CSRF
// ==========================
$token_post = GETPOST('token', 'alpha');
if (empty($token_post) || $token_post !== $_SESSION['newtoken']) {
    setEventMessages("❌ Error de seguridad: Token CSRF no válido.", null, 'errors');
    header("Location: ../../tpl/picaje.php");
    exit;
}

// ==============================
//  RECOGER DATOS DEL FORMULARIO
// ==============================
$tipo = GETPOST('tipo', 'alpha'); // entrada o salida
$fecha = date('Y-m-d');
$hora = date('H:i:s');
$usuario_id = $user->id;
$latitud = GETPOST('latitud');
$longitud = GETPOST('longitud');

// Validar tipo
if (!in_array($tipo, ['entrada', 'salida'])) {
    setEventMessages("❌ Tipo de picaje no válido", null, 'errors');
    header("Location: ../../tpl/picaje.php");
    exit;
}

// ===========================
//  LÓGICA DE FLUJO DE PICAJE
// ===========================

// Comprobar registros previos del día
$sql_check = "SELECT tipo, hora FROM llx_picaje 
              WHERE usuario_id = " . (int) $usuario_id . " 
              AND fecha = '" . $db->escape($fecha) . "' 
              ORDER BY hora ASC";

$res_check = $db->query($sql_check);

$ha_entrada = false;
$ha_salida = false;
$hora_entrada = null;

if ($res_check && $db->num_rows($res_check) > 0) {
    while ($obj = $db->fetch_object($res_check)) {
        if ($obj->tipo === 'entrada') {
            $ha_entrada = true;
            $hora_entrada = $obj->hora;
        }
        if ($obj->tipo === 'salida') {
            $ha_salida = true;
        }
    }
}

// =========================
//  CONTROL DE FLUJO LÓGICO
// =========================

if ($tipo === 'entrada' && $ha_entrada) {
    setEventMessages("⚠️ Ya has registrado una entrada hoy.", null, 'warnings');
    header("Location: ../../tpl/picaje.php");
    exit;
}

if ($tipo === 'salida' && !$ha_entrada) {
    setEventMessages("⚠️ No puedes registrar salida sin haber picado entrada antes.", null, 'warnings');
    header("Location: ../../tpl/picaje.php");
    exit;
}

if ($tipo === 'salida' && $ha_salida) {
    setEventMessages("⚠️ Ya has registrado una salida hoy.", null, 'warnings');
    header("Location: ../../tpl/picaje.php");
    exit;
}

// ================================
//  DETECCIÓN DE SALIDA ANTICIPADA
// ================================

$salida_manual = 0;
$justificacion = null;

if ($tipo === 'salida') {
    require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

    // Obtener hora prevista de salida (según config y usuario)
    $horario = getHorarioUsuario($usuario_id);
    $hora_salida_teorica = $horario->hora_salida;

    // Si la hora actual es menor a la hora prevista → marcar como manual
    if (strtotime($hora) < strtotime($hora_salida_teorica)) {
        $salida_manual = 1;

        // Nota: si más adelante quieres usar un modal para pedir justificación,
        // aquí puedes capturarla como POST['justificacion']
        $justificacion = GETPOST('justificacion', 'restricthtml');
            if (!$justificacion) $justificacion = 'Salida anticipada (sin justificar)';
    }
}

// =========================================
//  GUARDAR EL REGISTRO EN LA BASE DE DATOS
// =========================================

$sql = "INSERT INTO llx_picaje (fecha, hora, tipo, usuario_id, latitud, longitud, salida_manual, justificacion)
        VALUES (
            '" . $db->escape($fecha) . "',
            '" . $db->escape($hora) . "',
            '" . $db->escape($tipo) . "',
            " . (int) $usuario_id . ",
            " . ($latitud ? "'" . $db->escape($latitud) . "'" : "NULL") . ",
            " . ($longitud ? "'" . $db->escape($longitud) . "'" : "NULL") . ",
            $salida_manual,
            " . ($justificacion ? "'" . $db->escape($justificacion) . "'" : "NULL") . "
        )";

// =====================
//  EJECUTAR Y FINALIZAR
// =====================

if ($db->query($sql)) {
    setEventMessages("✅ Picaje de $tipo registrado correctamente.", null, 'mesgs');
} else {
    setEventMessages("❌ Error al registrar el picaje: " . $db->lasterror(), null, 'errors');
}

// Redirigir a la vista de picaje con retardo de 3 segundos
echo "✅ Redirigiendo...";
header("Refresh: 3; URL=../../tpl/picaje.php");
exit;


