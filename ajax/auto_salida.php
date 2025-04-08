<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

/**
 * Ejecuta la salida automática de un usuario si corresponde
 * 
 * @param int $user_id
 * @return bool
 */
function ejecutarSalidaAutomaticaUsuario($user_id)
{
    global $db, $conf;


    if (empty($conf->global->PICAR_SALIDA_AUTOMATICA)) {
        return false;
    }

    // Verificar si la opción está activada
    if (empty($conf->global->PICAR_SALIDA_AUTOMATICA)) {
        return false;
    }

    $fecha_actual = date('Y-m-d');
    $fecha_hora_actual = date('Y-m-d H:i:s');

    // Verificar si el usuario tiene entrada ese día
    $sql = "SELECT COUNT(*) as total FROM llx_picaje 
            WHERE fk_user = $user_id 
              AND DATE(fecha_hora) = '$fecha_actual' 
              AND tipo = 'entrada'";
    $res = $db->query($sql);
    if (!$res || !$db->num_rows($res)) return false;
    $row = $db->fetch_object($res);
    if ((int)$row->total === 0) return false;

    // Verificar si ya tiene salida
    $sql = "SELECT COUNT(*) as total FROM llx_picaje 
            WHERE fk_user = $user_id 
              AND DATE(fecha_hora) = '$fecha_actual' 
              AND tipo = 'salida'";
    $res = $db->query($sql);
    $row = $db->fetch_object($res);
    if ((int)$row->total > 0) return false;

    // Obtener hora de salida del usuario
    $horario = getHorarioUsuario($user_id);
    if (!$horario || empty($horario->hora_salida)) return false;

    $hora_actual = date('H:i:s');
    if (strtotime($hora_actual) < strtotime($horario->hora_salida)) {
        return false;
    }

    // Registrar salida automática
    $sql_insert = "INSERT INTO llx_picaje (fecha_hora, tipo, fk_user, tipo_registro)
                   VALUES (
                       '" . $db->escape($fecha_hora_actual) . "',
                       'salida',
                       $user_id,
                       'auto'
                   )";

    if ($db->query($sql_insert)) {
        $_SESSION['salida_auto_salida'] = 1; 
        return true;

    }
}

