<?php
// Definir entorno de ejecución
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF'] = '/dolibarr/custom/picaje/scripts/registrar_ausencias_diarias.php'; 

// Constantes para evitar chequeos de seguridad de Dolibarr
define('NOREQUIREMENU', 1);
define('NOREQUIREHTML', 1);
define('NOLOGIN', 1);
define('NOTOKENRENEWAL', 1);
define('NOCSRFCHECK', 1);
define('NOSCANPOSTFORINJECTION', 1);
define('NOSCANGETFORINJECTION', 1);
define('EVEN_IF_ONLY_LOGIN_ALLOWED', 1);

require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

// Obtener fecha actual en formato timestamp para comparación
$fechaHoy = strtotime(date('Y-m-d'));

// Obtener todos los usuarios con vacaciones activas hoy
$sql = "
    SELECT h.rowid, h.fk_user, h.date_debut, h.date_fin, ht.code, ht.label
    FROM llx_holiday AS h
    LEFT JOIN llx_c_holiday_types AS ht ON h.fk_type = ht.rowid
    WHERE h.statut = 3;
";

$resql = $db->query($sql);
if ($resql) {
    while ($obj = $db->fetch_object($resql)) {
        $fechaInicio = strtotime($obj->date_debut);
        $fechaFin = strtotime($obj->date_fin);

        // Si la ausencia cubre el día actual
        if ($fechaHoy >= $fechaInicio && $fechaHoy <= $fechaFin) {

            // Determinar tipo de ausencia
            $tipo = strtolower($obj->code);
            switch ($tipo) {
                case 'leave_sick':       $tipoAusencia = 'baja';        break;
                case 'leave_other':      $tipoAusencia = 'otra';        break;
                case 'leave_paid':
                case 'leave_paid_fr':
                case 'leave_rtt_fr':     $tipoAusencia = 'vacaciones';  break;
                default:                 $tipoAusencia = 'permiso';     break;
            }

            // Verificar si ya existe registro para hoy de este tipo
            $sql_check = "SELECT COUNT(*) as count
                          FROM llx_picaje
                          WHERE fk_user = " . (int)$obj->fk_user . "
                          AND DATE(fecha_hora) = '" . $db->escape(date('Y-m-d')) . "'
                          AND tipo = '" . $db->escape($tipoAusencia) . "'";
            $res_check = $db->query($sql_check);
            $count = 0;
            if ($res_check) {
                $row = $db->fetch_object($res_check);
                $count = (int)$row->count;
            }

            if ($count == 0) {
                $fechaActual = date('Y-m-d H:i:s');
                $sql_insert = "INSERT INTO llx_picaje (fk_user, fecha_hora, tipo, tipo_registro)
                               VALUES (" . (int)$obj->fk_user . ", '" . $db->escape($fechaActual) . "', '" . $db->escape($tipoAusencia) . "', 'auto_ausencia')";
                $db->query($sql_insert);
            }
        }
    }
} else {
    print 'Error en la consulta: '.$db->lasterror();
}

echo "Ausencias por vacaciones registradas correctamente.";
?>
