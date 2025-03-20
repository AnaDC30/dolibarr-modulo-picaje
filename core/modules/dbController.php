<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';

global $db, $user;

// Función para obtener registros del usuario actual
function obtenerRegistrosDiarios() {
    global $db, $user;

    $fecha_actual = date('Y-m-d');
    $sql = "SELECT hora, tipo FROM llx_picaje WHERE fecha = '" . $db->escape($fecha_actual) . "' 
            AND usuario_id = " . (int) $user->id . " ORDER BY hora ASC";
    
    $resql = $db->query($sql);
    $registros = [];

    if ($resql) {
        while ($row = $db->fetch_object($resql)) {
            $registros[] = [
                'hora' => $row->hora,
                'tipo' => ucfirst($row->tipo)
            ];
        }
    }

    return $registros;
}

/**
 * Obtener el historial completo de picajes para el usuario autenticado.
 */
function obtenerHistorialPicajes() {
    global $db, $user;

    $sql = "SELECT id, fecha, hora, tipo FROM llx_picaje WHERE usuario_id = " . (int) $user->id . " 
        ORDER BY fecha DESC, hora DESC";

    
    $resql = $db->query($sql);
    $historial = [];

    if ($resql) {
        while ($row = $db->fetch_object($resql)) {
            $historial[] = [
                'id' => $row->id,
                'fecha' => $row->fecha,
                'hora' => $row->hora,
                'tipo' => ucfirst($row->tipo)
            ];
        }
    }

    return $historial;
}
?>
