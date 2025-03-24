<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';

//Controlador de la base de datos

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

 // Obtener el historial completo de picajes para el usuario autenticado.

 function obtenerHistorialPicajes() {
    global $db, $user;

    if ($user->admin == 1) {
        // Mostrar todos los registros con nombre completo del usuario
        $sql = "SELECT p.id, p.fecha, p.hora, p.tipo, 
                       CONCAT(u.firstname, ' ', u.lastname) AS usuario
                FROM llx_picaje p
                LEFT JOIN " . MAIN_DB_PREFIX . "user u ON u.rowid = p.usuario_id
                ORDER BY p.fecha DESC, p.hora DESC";
    } else {
        // Mostrar solo los registros del usuario logueado
        $sql = "SELECT p.id, p.fecha, p.hora, p.tipo
                FROM llx_picaje p
                WHERE p.usuario_id = " . (int) $user->id . "
                ORDER BY p.fecha DESC, p.hora DESC";
    }

    $resql = $db->query($sql);
    $historial = [];

    if ($resql) {
        while ($row = $db->fetch_object($resql)) {
            $historial[] = [
                'id' => $row->id,
                'fecha' => $row->fecha,
                'hora' => $row->hora,
                'tipo' => ucfirst($row->tipo),
                'usuario' => $row->usuario ?? null
            ];
        }
    }

    return $historial;
}



// función para obtener un registro específico por ID 
function obtenerPicajePorId($id) {
    global $db;
    $sql = "SELECT * FROM llx_picaje WHERE id = " . (int)$id;
    $resql = $db->query($sql);
    if ($resql && $db->num_rows($resql) > 0) {
        return $db->fetch_array($resql); 
    }
    return null;
}
?>
