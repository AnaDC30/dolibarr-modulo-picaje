<?php
// =====================
// CARGA DE ENTORNO
// =====================
require_once $_SERVER["DOCUMENT_ROOT"] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/security.lib.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

header('Content-Type: application/json');

// =====================
// VALIDACIÓN DE SESIÓN Y TOKEN
// =====================
if (empty($user->id)) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

$token = GETPOST('token', 'alpha');
if (empty($_SESSION['newtoken']) || $token !== $_SESSION['newtoken']) {
    echo json_encode(['success' => false, 'error' => 'Token CSRF inválido']);
    exit;
}


// =====================
// RECOGER DATOS
// =====================
$tipo = GETPOST('tipo', 'alpha');
$justificacion = trim(GETPOST('justificacion', 'restricthtml'));

// Validaciones básicas
if (!in_array($tipo, ['horas_extra', 'salida_anticipada', 'olvido_picaje']) || empty($justificacion)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$fecha = date('Y-m-d');
$hora = date('H:i:s');

// =====================
// INSERTAR EN BASE DE DATOS
// =====================
$sql = "INSERT INTO " . MAIN_DB_PREFIX . "picaje_incidencias (fk_user, fecha, hora, tipo, justificacion, entity)
        VALUES (" . (int) $user->id . ", '" . $db->escape($fecha) . "', '" . $db->escape($hora) . "', 
        '" . $db->escape($tipo) . "', '" . $db->escape($justificacion) . "', " . (int) $conf->entity . ")";

if ($db->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $db->lasterror()]);
}
exit;
