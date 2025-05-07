#!/usr/bin/env php
<?php
define('NOREQUIREMENU', 1);
define('NOLOGIN', 1);
define('NOREQUIREHTML', 1);
define('NOTOKENRENEWAL', 1);
define('NOCSRFCHECK', 1);
define('NOSCANPOSTFORINJECTION', 1);
define('NOSCANGETFORINJECTION', 1);
define('EVEN_IF_ONLY_LOGIN_ALLOWED', 1);

$_SERVER['REQUEST_METHOD']  = 'GET';
$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF']        = '/dolibarr/custom/picaje/scripts/picaje_autoentry.php';

require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/autoentrada.php';

$logfile = DOL_DOCUMENT_ROOT . '/custom/picaje/scripts/picaje_autoentry.log';
file_put_contents($logfile, date('Y-m-d H:i:s') . " — Inicio picaje_autoentry\n", FILE_APPEND);

$sql = "SELECT rowid FROM " . MAIN_DB_PREFIX . "user WHERE statut = 1";
$resql = $db->query($sql);
if ($resql) {
    while ($u = $db->fetch_object($resql)) {
        $uid = (int)$u->rowid;
        $ok = ejecutarEntradaAutomaticaUsuario($uid);
        $line = sprintf(" User %3d → %s\n", $uid, $ok ? 'ENTRADA INSERTADA' : 'SIN CAMBIO');
        file_put_contents($logfile, $line, FILE_APPEND);
    }
} else {
    file_put_contents($logfile, "❌ Error en la consulta SQL: " . $db->lasterror() . "\n", FILE_APPEND);
}

file_put_contents($logfile, date('Y-m-d H:i:s') . " — Fin picaje_autoentry\n\n", FILE_APPEND);
$db->close();
