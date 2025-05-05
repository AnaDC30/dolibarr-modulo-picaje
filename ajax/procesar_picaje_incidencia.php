<?php
// ==============================
//  AJAX: Crear picaje vinculado a incidencia
// ==============================
define('NOCSRFCHECK', 1);
define('NOTOKENRENEWAL', 1);
define('NOREQUIREMENU', 1);
define('NOREQUIREHTML', 1);
define('AJAX', 1);

require_once $_SERVER['DOCUMENT_ROOT'].'/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/picaje/class/picaje.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

header('Content-Type: application/json');

global $user, $db;
$response = ['success' => false];

// 1) Sesión y permisos
if (empty($user->id)) {
    $response['error'] = 'Sesión no válida o expirada.';
    echo json_encode($response);
    exit;
}
if ($user->admin != 1) {
    $response['error'] = 'Acceso no autorizado.';
    echo json_encode($response);
    exit;
}

// 2) Datos obligatorios
$userAssign   = GETPOST('fk_user', 'int');
$incidenciaId = GETPOST('incidencia', 'int');
$tipo         = GETPOST('tipo', 'alpha');    // 'entrada' o 'salida'
$fechaNueva   = GETPOST('fecha', 'alpha');   // 'YYYY-MM-DD'
$horaNueva    = GETPOST('hora', 'alpha');    // 'HH:MM'
$comentario   = GETPOST('comentario', 'text');
$lat          = GETPOST('latitud', 'alpha');
$lng          = GETPOST('longitud', 'alpha');

if (empty($incidenciaId) || !$tipo || !$fechaNueva || !$horaNueva || empty($userAssign) || !is_numeric($userAssign)) {
    $response['error'] = 'Faltan datos obligatorios (usuario, incidencia, tipo, fecha u hora).';
    echo json_encode($response);
    exit;
}

// 3) Validar incidencia en base de datos (⚠ movido aquí porque depende de $userAssign)
$sqlCheck = "SELECT fk_user FROM llx_picaje_incidencias WHERE rowid = " . (int)$incidenciaId . " AND status = 'Pendiente'";
$resCheck = $db->query($sqlCheck);
$objCheck = $db->fetch_object($resCheck);

if (!$objCheck) {
    $response['error'] = 'La incidencia no existe o ya fue resuelta.';
    echo json_encode($response);
    exit;
}

if ($objCheck->fk_user != $userAssign) {
    $response['error'] = 'La incidencia no pertenece al usuario seleccionado.';
    echo json_encode($response);
    exit;
}

// 4) Validar formato fecha/hora
$timestamp = strtotime("{$fechaNueva}T{$horaNueva}");
if ($timestamp === false) {
    $response['error'] = 'Formato de fecha u hora no válido.';
    echo json_encode($response);
    exit;
}
$fechaHoraSQL = date('Y-m-d H:i:s', $timestamp);

// 5) Construir e insertar picaje
$sqlInsert = "
    INSERT INTO llx_picaje
    (fk_user, tipo, fk_incidencia, comentario, fecha_hora, latitud, longitud, tipo_registro)
    VALUES (
        ".(int)$userAssign.",
        '".$db->escape($tipo)."',
        ".(int)$incidenciaId.",
        '".$db->escape($comentario)."',
        '".$db->escape($fechaHoraSQL)."',
        ".($lat ? "'".$db->escape($lat)."'" : "NULL").",
        ".($lng ? "'".$db->escape($lng)."'" : "NULL").",
        'manual'
    )
";

$resInsert = $db->query($sqlInsert);
if ($resInsert) {
    // 6) Marcar incidencia como resuelta (⚠ solo si el insert fue exitoso)
    $sqlUpdate = "UPDATE llx_picaje_incidencias SET status = 'Resuelta' WHERE rowid = " . (int)$incidenciaId;
    $db->query($sqlUpdate);

    $response['success'] = true;
    $response['message'] = 'Picaje de incidencia creado correctamente.';
} else {
    $response['error'] = 'Error al crear picaje: ' . $db->lasterror();
}

echo json_encode($response);
exit;
