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

 function obtenerHistorialPicajes($filtroFecha = null, $filtroUsuario = null) {
    global $db, $user;

    $sql = "SELECT p.id, p.fecha, p.hora, p.tipo";
    if ($user->admin == 1) {
        $sql .= ", CONCAT(u.firstname, ' ', u.lastname) AS usuario";
    }
    $sql .= " FROM llx_picaje p";

    if ($user->admin == 1) {
        $sql .= " LEFT JOIN llx_user u ON u.rowid = p.usuario_id";
    }

    $where = [];

    // Filtro por usuario (solo admin)
    if ($user->admin == 1 && !empty($filtroUsuario)) {
        $filtroUsuarioEscapado = $db->escape($filtroUsuario);
        $where[] = "(u.firstname LIKE '%$filtroUsuarioEscapado%' OR u.lastname LIKE '%$filtroUsuarioEscapado%')";
    }

    // Filtro por fecha
    if (!empty($filtroFecha)) {
        $filtroFechaEscapado = $db->escape($filtroFecha);
        $where[] = "p.fecha = '$filtroFechaEscapado'";
    }

    // Si no es admin, mostrar solo sus registros
    if ($user->admin != 1) {
        $where[] = "p.usuario_id = " . (int) $user->id;
    }

    if (count($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY p.fecha DESC, p.hora DESC";

    $resql = $db->query($sql);
    $historial = [];

    if ($resql) {
        while ($row = $db->fetch_object($resql)) {
            $historial[] = [
                'id' => $row->id,
                'fecha' => $row->fecha,
                'hora' => $row->hora,
                'tipo' => ucfirst($row->tipo),
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
    global $db, $conf;

    // 1. Buscar horario individual
    $sql = "SELECT hora_salida, salida_automatica 
            FROM llx_picaje_horarios 
            WHERE fk_user = " . (int) $user_id . " 
            AND entity = " . (int) $conf->entity . " 
            LIMIT 1";

    $resql = $db->query($sql);
    if ($resql && $db->num_rows($resql)) {
        return $db->fetch_object($resql);
    }

    // 2. Si no hay horario individual, obtener horario por grupo
    $sql_dept = "SELECT g.rowid, g.nom FROM llx_usergroup_user u
                JOIN llx_usergroup g ON g.rowid = u.fk_usergroup
                WHERE u.fk_user = " . (int) $user_id;

    $res_dept = $db->query($sql_dept);
    if ($res_dept && $db->num_rows($res_dept)) {
        while ($obj = $db->fetch_object($res_dept)) {
            $sql2 = "SELECT hora_salida, salida_automatica 
                    FROM llx_picaje_horarios 
                    WHERE fk_departement = " . (int) $obj->rowid . " 
                    AND entity = " . (int) $conf->entity . " 
                    LIMIT 1";
            $res2 = $db->query($sql2);
            if ($res2 && $db->num_rows($res2)) {
                $horario = $db->fetch_object($res2);
                $horario->heredado_de_grupo = $obj->nom; // añadimos info del grupo
            return $horario;
            }
        }
    }



    // 3. Si no hay nada definido, usar duración por defecto del módulo
    $duracion = getDolGlobalInt('PICAR_DURACION_JORNADA') ?: 8;
    $hora_salida = date("H:i:s", strtotime("+$duracion hours", strtotime("08:00"))); // Por defecto desde las 08:00

    return (object) [
        'hora_salida' => $hora_salida,
        'salida_automatica' => getDolGlobalInt('PICAR_SALIDA_AUTOMATICA')
    ];
}

//Funcion estado de picaje del Usuario 

function getEstadoPicajeUsuario($user_id)
{
    global $db;

    $fecha = date('Y-m-d');
    $sql = "SELECT tipo FROM llx_picaje 
            WHERE usuario_id = " . (int) $user_id . " 
            AND fecha = '" . $db->escape($fecha) . "'";

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


?>
