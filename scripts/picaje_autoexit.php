#!/usr/bin/env php
<?php
// === Defines CLI ===
define('NOREQUIREMENU',       1);
define('NOLOGIN',             1);
define('NOREQUIREHTML',       1);
define('NOTOKENRENEWAL',      1);
define('NOCSRFCHECK',         1);
define('NOSCANPOSTFORINJECTION', 1);
define('NOSCANGETFORINJECTION',  1);
define('EVEN_IF_ONLY_LOGIN_ALLOWED', 1);

// Simular GET
$_SERVER['REQUEST_METHOD']  = 'GET';
$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF']        = '/dolibarr/custom/picaje/scripts/picaje_autoexit.php';

// Carga Dolibarr
require_once dirname(__DIR__, 3) . '/main.inc.php';

// Incluye librerías
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/autosalida.php';

// Preparamos el log
$logfile = DOL_DOCUMENT_ROOT . '/custom/picaje/scripts/picaje_autoexit.log';
file_put_contents($logfile, date('Y-m-d H:i:s') . " — Inicio picaje_autoexit\n", FILE_APPEND);

// Recorre todos los usuarios activos
$sql   = "SELECT rowid FROM " . MAIN_DB_PREFIX . "user WHERE statut = 1";
$resql = $db->query($sql);
if ($resql) {
    while ($u = $db->fetch_object($resql)) {
        $uid = (int)$u->rowid;
        // Intentamos la auto-salida
        $ok = ejecutarSalidaAutomaticaUsuario($uid);
        // Logueamos el resultado
        $line = sprintf(" User %3d → %s\n", 
                        $uid, $ok ? 'INSERTADO' : 'SIN CAMBIO');
        file_put_contents($logfile, $line, FILE_APPEND);
    }
}

// Fin de ejecución
file_put_contents($logfile, date('Y-m-d H:i:s') . " — Fin picaje_autoexit\n\n", FILE_APPEND);

// Cerramos conexión
$db->close();
