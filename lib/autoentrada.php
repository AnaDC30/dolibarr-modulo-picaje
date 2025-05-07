<?php

function ejecutarEntradaAutomaticaUsuario($user_id, $latitude = null, $longitude = null)
{
    global $db, $conf;

    // 1. ¿Está habilitada la autoentrada?
    if (empty($conf->global->PICAJE_AUTO_LOGIN)) {
        return false;
    }

    // 2. ¿Ya tiene entrada hoy?
    $estado = getEstadoPicajeUsuario($user_id);
    if (!empty($estado['entrada'])) {
        return false;
    }

    // 3. ¿Ya es la hora teórica o posterior?
    $horario = getHorarioUsuario($user_id);
    $hora_entrada = $horario->hora_entrada ?: getHoraEntradaEmpresaPorDefecto();
    if (strtotime(date('H:i:s')) < strtotime($hora_entrada)) {
        return false;
    }

    // 4. Preparar y ejecutar INSERT de entrada
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

    if (!$res) {
        dol_syslog("Error en autoentrada para user $user_id: " . $db->lasterror(), LOG_ERR);
    }

    return $res ? true : false;
}

