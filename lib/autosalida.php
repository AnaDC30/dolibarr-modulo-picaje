<?php
/**
 * Ejecuta la salida automática de un usuario si corresponde
 *
 * @param int $user_id
 * @return bool
 */
function ejecutarSalidaAutomaticaUsuario($user_id, $lat, $lon)
{
    global $db, $conf;

    if (empty($conf->global->PICAR_SALIDA_AUTOMATICA)) {
        return false;
    }

    // 1) Verificar entrada pero no salida
    $estado = getEstadoPicajeUsuario($user_id);
    if (!$estado['entrada'] || $estado['salida']) {
        return false;
    }

    // 2) Calcular hora de salida (usuario o empresa)
    $horario = getHorarioUsuario($user_id);
    $hora_salida = $horario->hora_salida ?: getHoraSalidaEmpresaPorDefecto();
    if (strtotime(date('H:i:s')) < strtotime($hora_salida)) {
        return false;
    }

    // 3) Insertar registro de salida automática CON geo
    $fecha_hora = date('Y-m-d H:i:s');

   $input = json_decode(file_get_contents('php://input'), true);
    $lat = $input['latitud'] ?? null;
    $lon = $input['longitud'] ?? null;


    $sql = "
      INSERT INTO ".MAIN_DB_PREFIX."picaje
        (fecha_hora, tipo, fk_user, tipo_registro, latitud, longitud)
      VALUES
        ('".$db->escape($fecha_hora)."',
         'salida',
         ".(int)$user_id.",
         'auto',
         {$lat},
         {$lon}
        )";

    if ($db->query($sql)) {
        $_SESSION['salida_auto_salida'] = 1;
        return true;
    }

    return false;
}
