<?php
class PicajePanelController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function registrarPicajeInteligente($user_id, $latitud = null, $longitud = null) {
        $sql = "SELECT tipo FROM llx_picaje 
                WHERE fk_user = " . (int) $user_id . " 
                AND DATE(fecha_hora) = '" . $this->db->escape(date('Y-m-d')) . "'
                ORDER BY fecha_hora ASC";

        $resql = $this->db->query($sql);
        $tipo = 'entrada';

        if ($resql && $this->db->num_rows($resql) > 0) {
            $tipos = [];
            while ($obj = $this->db->fetch_object($resql)) {
                $tipos[] = $obj->tipo;
            }

            // Si ya hay más entradas que salidas, siguiente debe ser salida
            $countEntrada = count(array_filter($tipos, fn($t) => $t === 'entrada'));
            $countSalida  = count(array_filter($tipos, fn($t) => $t === 'salida'));

            if ($countEntrada > $countSalida) {
                $tipo = 'salida';
            }
        }

        $now = dol_now();
        $this->db->begin();

        $sqlInsert = "INSERT INTO llx_picaje (fk_user, fecha_hora, tipo, tipo_registro, latitud, longitud) 
        VALUES (
            " . (int)$user_id . ",
            '" . $this->db->idate($now) . "',
            '" . $this->db->escape($tipo) . "',
            'panel',
            " . ($latitud !== null ? "'" . $this->db->escape($latitud) . "'" : "NULL") . ",
            " . ($longitud !== null ? "'" . $this->db->escape($longitud) . "'" : "NULL") . "
        )";


        $resInsert = $this->db->query($sqlInsert);

        if ($resInsert) {
            $this->db->commit();
            $siguiente = $tipo === 'entrada' ? 'salida' : 'entrada';

            return [
                'mensaje' => "Picada de $tipo registrada correctamente.",
                'siguiente' => $siguiente
            ];
        } else {
            $this->db->rollback();
            return [
                'mensaje' => "Error al registrar la picada.",
                'siguiente' => $tipo
            ];
        }
    }
}
