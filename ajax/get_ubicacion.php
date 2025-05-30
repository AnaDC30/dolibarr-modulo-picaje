<?php
require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

header('Content-Type: application/json');

// Validar sesión
global $user;
if (!$user || empty($user->id)) {
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit;
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

$id = (int) $_GET['id'];
global $db;

$sql = "SELECT p.latitud, p.longitud, p.fecha_hora,
               CONCAT(u.firstname, ' ', u.lastname) AS usuario
        FROM llx_picaje p
        LEFT JOIN llx_user u ON u.rowid = p.fk_user
        WHERE p.id = $id";

$resql = $db->query($sql);

if ($resql && $db->num_rows($resql) > 0) {
    $data = $db->fetch_object($resql);
    echo json_encode([
        'success' => true,
        'latitud' => $data->latitud,
        'longitud' => $data->longitud,
        'fecha' => dol_print_date($data->fecha_hora, '%d/%m/%Y'),
        'hora' => dol_print_date($data->fecha_hora, '%H:%M:%S'),
        'usuario' => $data->usuario
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Registro no encontrado']);
}
exit;
