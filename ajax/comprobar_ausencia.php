<?php
define('NOREQUIREMENU', 1);
define('NOREQUIREHTML', 1);
define('NOTOKENRENEWAL', 1);
define('NOCSRFCHECK', 1);

require_once dirname(__DIR__, 3) . '/main.inc.php';



global $user;

header('Content-Type: application/json');

if (empty($user->id) || $user->id <= 0) {
    echo json_encode(['error' => 'No hay sesión activa', 'ausente' => false]);
    exit;
}


$userid = (int)$user->id;

$fechaHoy = date('Y-m-d');

$debug = "🧪 COMPROBAR AUSENCIA\n";
$debug .= "User login: " . $user->login . "\n";
$debug .= "Usuario: $userid\nFecha: $fechaHoy\n";

// Consulta explícita de ausencias
$sql = "SELECT tipo, fecha_hora FROM llx_picaje
        WHERE fk_user = " . (int)$userid . "
        AND DATE(fecha_hora) = '" . $db->escape($fechaHoy) . "'
        AND tipo IN ('vacaciones', 'baja', 'permiso', 'otra')";

$debug .= "SQL ejecutada: $sql\n";

$res = $db->query($sql);

if ($res && $db->num_rows($res) > 0) {
    $row = $db->fetch_object($res);
    $debug .= "✔️ Ausencia encontrada: {$row->tipo} ({$row->fecha_hora})\n";
    file_put_contents(__DIR__ . '/debug_ausencia.log', $debug);
    echo json_encode(['ausente' => true, 'tipo' => $row->tipo]);
} else {
    $debug .= "❌ No se detectó ausencia\n";
    file_put_contents(__DIR__ . '/debug_ausencia.log', $debug);
    echo json_encode(['ausente' => false]);
}
