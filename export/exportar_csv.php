<?php
require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

global $db, $user;

// Recoger filtros desde la URL
$filtroFecha = GETPOST('fecha', 'alpha');
$filtroUsuario = GETPOST('usuario', 'alpha');
$filtroUserId = GETPOST('user_id', 'int');
$desdeIncidencias = GETPOST('desde', 'alpha');

// Obtener datos filtrados
$historial = obtenerHistorialPicajes($filtroFecha, $filtroUsuario, $filtroUserId, $desdeIncidencias);

// Configurar encabezados para descarga
$filename = 'historial_picaje_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Abrir salida estándar como archivo
$output = fopen('php://output', 'w');

// Encabezados CSV
$headers = ['ID', 'Fecha', 'Hora', 'Tipo', 'Origen'];
if ($user->admin) {
    $headers[] = 'Usuario';
}
fputcsv($output, $headers);

// Filas
foreach ($historial as $row) {
    $linea = [
        $row['id'],
        $row['fecha'],
        $row['hora'],
        $row['tipo'],
        $row['tipo_registro']
    ];
    if ($user->admin) {
        $linea[] = $row['usuario'];
    }
    fputcsv($output, $linea);
}

fclose($output);
exit;
