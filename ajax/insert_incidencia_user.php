<?php
// --- Evita salida de HTML y menús ---
define('NOREQUIREMENU', '1');
define('NOREQUIREHTML', '1');
define('NOTOKENRENEWAL', '1');
define('NOCSRFCHECK', '1');

require_once dirname(__DIR__, 3) . '/main.inc.php';


global $db, $user, $conf;

header('Content-Type: application/json');

$tipo = trim(GETPOST('tipo', 'alpha'));
$comentario = trim(GETPOST('comentario', 'restricthtml'));

if (empty($tipo) || empty($comentario)) {
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios.']);
    exit;
}

$fecha = date('Y-m-d');
$hora = date('H:i:s');

$sql = "INSERT INTO llx_picaje_incidencias 
        (fk_user, tipo, comentario, fecha, hora, status, entity, resolucion)
        VALUES (
            " . (int) $user->id . ",
            '" . $db->escape($tipo) . "',
            '" . $db->escape($comentario) . "',
            '" . $fecha . "',
            '" . $hora . "',
            'Pendiente',
            " . (int) $conf->entity . ",
            NULL
        )";
        

$res = $db->query($sql);

if ($res) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos']);
}
