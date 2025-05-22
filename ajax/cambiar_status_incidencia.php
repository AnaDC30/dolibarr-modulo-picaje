<?php
// --- Evita salida de HTML y menús ---
define('NOREQUIREMENU', '1');
define('NOREQUIREHTML', '1');
define('NOTOKENRENEWAL', '1');
define('NOCSRFCHECK', '1');

require_once dirname(__DIR__, 3) . '/main.inc.php';


global $db, $user;

header('Content-Type: application/json');

// Verificamos que hay sesión activa y que es admin
if (empty($user->id) || !$user->admin) {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

$id = GETPOST('id', 'int');
$status = GETPOST('status', 'alpha');
$resolucion = trim(GETPOST('resolucion', 'restricthtml'));

if ($id <= 0 || empty($status)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Preparamos SQL
$sql = "UPDATE llx_picaje_incidencias 
        SET status = '" . $db->escape($status) . "',
         resolucion = " . ($resolucion ? "'" . $db->escape($resolucion) . "'" : "NULL") . "
         WHERE rowid = " . (int) $id;

// Ejecutamos
$res = $db->query($sql);

echo json_encode(['success' => $res ? true : false]);
