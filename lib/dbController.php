<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';

//Controlador de la base de datos

global $db, $user;


// Función para obtener registros del usuario actual
function obtenerRegistrosDiarios() {
    global $db, $user;

    $sql = "SELECT 
                TIME(fecha_hora) AS hora, 
                tipo, 
                tipo_registro 
            FROM llx_picaje
            WHERE fk_user = " . (int) $user->id . "
            AND DATE(fecha_hora) = '" . date('Y-m-d') . "'
            ORDER BY fecha_hora ASC";

    $resql = $db->query($sql);
    $registros = [];

    if ($resql) {
        while ($row = $db->fetch_object($resql)) {
            $registros[] = [
                'hora' => $row->hora,
                'tipo' => ucfirst($row->tipo),
                'origen' => $row->tipo_registro
            ];
        }
    }

    return $registros;
}

// Obtener el historial completo de picajes para el usuario autenticado.
function obtenerHistorialPicajes($filtroFecha = null, $filtroUsuario = null) {
    global $db, $user;

    $sql = "SELECT p.id, DATE(p.fecha_hora) AS fecha, TIME(p.fecha_hora) AS hora, p.tipo, p.tipo_registro";
    if ($user->admin == 1) {
        $sql .= ", CONCAT(u.firstname, ' ', u.lastname) AS usuario";
    }
    $sql .= " FROM llx_picaje p";

    if ($user->admin == 1) {
        $sql .= " LEFT JOIN llx_user u ON u.rowid = p.fk_user";
    }

    $where = [];

    // Filtro por usuario (solo admin)
    if ($user->admin == 1 && strlen(trim($filtroUsuario)) >= 2) {
        $filtroUsuarioEscapado = $db->escape($filtroUsuario);
        $where[] = "(u.firstname LIKE '%$filtroUsuarioEscapado%' OR u.lastname LIKE '%$filtroUsuarioEscapado%')";
    }

    // Filtro por fecha (usamos fecha_hora)
    if (isset($filtroFecha) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filtroFecha)) {
        $filtroFechaEscapado = $db->escape($filtroFecha);
        $where[] = "DATE(p.fecha_hora) = '$filtroFechaEscapado'";
    }
    

    // Si no es admin, mostrar solo sus registros
    if ($user->admin != 1) {
        $where[] = "p.fk_user = " . (int) $user->id;
    }

    if (count($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY p.fecha_hora DESC";

    $resql = $db->query($sql);
    $historial = [];

    if ($resql) {
        while ($row = $db->fetch_object($resql)) {
            $historial[] = [
                'id' => $row->id,
                'fecha' => $row->fecha,
                'hora' => $row->hora,
                'tipo' => ucfirst($row->tipo),
                'tipo_registro' => $row->tipo_registro ?? 'manual',
                'usuario' => $user->admin == 1 ? $row->usuario : null
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

function getHorarioUsuario($user_id)
{
    global $db;

    $extrafields = new ExtraFields($db);
    $user = new User($db);
    if ($user->fetch($user_id) <= 0) return null;

    $user->fetch_optionals($user_id, $extrafields);
    $hora_salida = $user->array_options['options_picaje_hora_salida'] ?? null;

    // Si no tiene horario asignado, buscamos en su grupo
    if (!$hora_salida && $user->fk_usergroup) {
        require_once DOL_DOCUMENT_ROOT . '/user/class/usergroup.class.php';
        $group = new UserGroup($db);
        if ($group->fetch($user->fk_usergroup) > 0) {
            $group->fetch_optionals($user->fk_usergroup, $extrafields);
            $hora_salida = $group->array_options['options_picaje_hora_salida'] ?? null;
        }
    }

    return (object)[
        'hora_salida' => $hora_salida
    ];
}


//Funcion estado de picaje del Usuario 

function getEstadoPicajeUsuario($user_id)
{
    global $db;

    $fecha = date('Y-m-d');
    $sql = "SELECT tipo FROM llx_picaje 
            WHERE fk_user = " . (int) $user_id . " 
            AND DATE(fecha_hora) = '" . $db->escape($fecha) . "'";

    $res = $db->query($sql);

    $estado = [
        'entrada' => false,
        'salida' => false
    ];

    if ($res && $db->num_rows($res)) {
        while ($obj = $db->fetch_object($res)) {
            if ($obj->tipo === 'entrada') $estado['entrada'] = true;
            if ($obj->tipo === 'salida') $estado['salida'] = true;
        }
    }

    return $estado;
}

function getHoraSalidaEmpresaPorDefecto() {
    $dias = [
        1 => 'MAIN_INFO_OPENINGHOURS_MONDAY',
        2 => 'MAIN_INFO_OPENINGHOURS_TUESDAY',
        3 => 'MAIN_INFO_OPENINGHOURS_WEDNESDAY',
        4 => 'MAIN_INFO_OPENINGHOURS_THURSDAY',
        5 => 'MAIN_INFO_OPENINGHOURS_FRIDAY',
        6 => 'MAIN_INFO_OPENINGHOURS_SATURDAY',
        7 => 'MAIN_INFO_OPENINGHOURS_SUNDAY',
    ];

    $diaSemana = date('N');
    $constante = $dias[$diaSemana] ?? null;
    $valor = $constante ? getDolGlobalString($constante) : '';

    if ($valor && strpos($valor, '-') !== false) {
        list(, $salida) = explode('-', $valor);
        if (preg_match('/^\d{1,2}$/', $salida)) $salida .= ':00:00';
        elseif (preg_match('/^\d{1,2}:\d{2}$/', $salida)) $salida .= ':00';
        return $salida;
    }

    return '14:00:00'; // fallback
}

function getNombreUsuarioPorId($id) {
    global $db;
    $sql = "SELECT firstname, lastname FROM " . MAIN_DB_PREFIX . "user WHERE rowid = " . (int) $id;
    $res = $db->query($sql);
    if ($res && $db->num_rows($res)) {
        $obj = $db->fetch_object($res);
        return trim($obj->firstname . ' ' . $obj->lastname);
    }
    return '';
}


?>