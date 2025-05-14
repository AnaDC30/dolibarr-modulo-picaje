<?php
require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

global $db, $conf, $user;
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$latitude = isset($data['latitud']) ? $data['latitud'] : null;
$longitude = isset($data['longitud']) ? $data['longitud'] : null;

$auto_entry = false;
$user_id = (int) $user->id;

if (empty($conf->global->PICAJE_AUTO_LOGIN)) {
    echo json_encode(['auto_entry' => false]);
    exit;
}

$estado = getEstadoPicajeUsuario($user_id);
if (!empty($estado['entrada'])) {
    echo json_encode(['auto_entry' => false]);
    exit;
}

$horario = getHorarioUsuario($user_id);
$hora_entrada = $horario->hora_entrada ?: getHoraEntradaEmpresaPorDefecto();
if (strtotime(date('H:i:s')) < strtotime($hora_entrada)) {
    echo json_encode(['auto_entry' => false]);
    exit;
}

$fecha_hora = date('Y-m-d H:i:s');
$lat = $latitude !== null ? "'" . $db->escape($latitude) . "'" : "NULL";
$lon = $longitude !== null ? "'" . $db->escape($longitude) . "'" : "NULL";

$sql = "
    INSERT INTO " . MAIN_DB_PREFIX . "picaje (
        fecha_hora, tipo, fk_user, tipo_registro, latitud, longitud
    ) VALUES (
        '" . $db->escape($fecha_hora) . "',
        'entrada',
        " . (int)$user_id . ",
        'auto_login',
        {$lat},
        {$lon}
    )";

$res = $db->query($sql);
echo json_encode(['auto_entry' => $res ? true : false]);
exit;
