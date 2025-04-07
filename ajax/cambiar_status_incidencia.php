<?php
// --- Evita salida de HTML y menús ---
define('NOREQUIREMENU', '1');
define('NOREQUIREHTML', '1');
define('NOTOKENRENEWAL', '1');
define('NOCSRFCHECK', '1');

require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';

global $db, $user;

header('Content-Type: application/json');

// Verificamos que hay sesión activa y que es admin
if (empty($user->id) || !$user->admin) {
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

$id = GETPOST('id', 'int');
$status = GETPOST('status', 'alpha');

if ($id <= 0 || empty($status)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$sql = "UPDATE llx_picaje_incidencias 
        SET status = '" . $db->escape($status) . "' 
        WHERE rowid = " . (int) $id;

$res = $db->query($sql);

echo json_encode(['success' => $res ? true : false]);
