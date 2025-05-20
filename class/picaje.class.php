<?php

class Picaje {
    public $db;
    public $id;
    public $fk_user;
    public $tipo;
    public $latitud;
    public $longitud;
    public $fecha_hora;
    public $salida_manual;
    public $justificacion;
    public $tipo_registro;

    public $table_element = 'llx_picaje';

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($user) {
        global $conf;

        $sql = "INSERT INTO " . $this->table_element . " (
            fk_user, tipo, latitud, longitud, fecha_hora,
            salida_manual, comentario, tipo_registro, entity
        )
        VALUES (
            " . (int) $this->fk_user . ",
            '" . $this->db->escape($this->tipo) . "',
            '" . $this->db->escape($this->latitud) . "',
            '" . $this->db->escape($this->longitud) . "',
            '" . $this->db->idate($this->fecha_hora) . "',
            " . (int) $this->salida_manual . ",
            " . ($this->justificacion ? "'" . $this->db->escape($this->justificacion) . "'" : "NULL") . ",
            '" . $this->db->escape($this->tipo_registro) . "',
            " . (int) $conf->entity . "
        )";


        dol_syslog(get_class($this) . "::create", LOG_DEBUG);

        $res = $this->db->query($sql);
        if ($res) {
            $this->id = $this->db->last_insert_id($this->table_element);
            return $this->id;
        } else {
            dol_syslog(get_class($this) . "::create error - " . $this->db->lasterror(), LOG_ERR);
            return -1;
        }
    }

    public static function getEstadoHoy($db, $user_id)
{
    $estado = ['entrada' => false, 'salida' => false];

    $sql = "SELECT tipo FROM llx_picaje
            WHERE fk_user = " . (int) $user_id . "
            AND DATE(fecha_hora) = CURDATE()";

    $res = $db->query($sql);
    if ($res) {
        while ($obj = $db->fetch_object($res)) {
            if ($obj->tipo == 'entrada') $estado['entrada'] = true;
            if ($obj->tipo == 'salida') $estado['salida'] = true;
        }
    }

    return $estado;
}

}
