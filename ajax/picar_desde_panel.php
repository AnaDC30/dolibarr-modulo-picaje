<?php
define('NOCSRFCHECK', 1);
define('NOREQUIREMENU', 1);
define('NOREQUIREHTML', 1);
define('NOTOKENRENEWAL', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/picajePanelController.php';

header('Content-Type: application/json');

// Obtener coordenadas del navegador si se han enviado
$data = json_decode(file_get_contents('php://input'), true);
$latitud = $data['latitud'] ?? null;
$longitud = $data['longitud'] ?? null;

$controller = new PicajePanelController($db);
$resultado = $controller->registrarPicajeInteligente($user->id, $latitud, $longitud);

// Calcular la siguiente picada lógica del usuario tras registrar
$sql = "SELECT tipo FROM llx_picaje 
        WHERE fk_user = " . (int)$user->id . " 
        AND DATE(fecha_hora) = '" . date('Y-m-d') . "' 
        ORDER BY fecha_hora ASC";

$resql = $db->query($sql);
$tipos = [];

if ($resql) {
    while ($obj = $db->fetch_object($resql)) {
        $tipos[] = $obj->tipo;
    }
}

$entradas = count(array_filter($tipos, fn($t) => $t === 'entrada'));
$salidas  = count(array_filter($tipos, fn($t) => $t === 'salida'));
$siguiente = ($entradas > $salidas) ? 'salida' : 'entrada';

echo json_encode([
    'exito'       => strpos($resultado['mensaje'], 'correctamente') !== false,
    'mensaje'     => $resultado['mensaje'],
    'siguiente'   => $siguiente,
    'anticipada'  => $resultado['anticipada'] ?? false  
]);
