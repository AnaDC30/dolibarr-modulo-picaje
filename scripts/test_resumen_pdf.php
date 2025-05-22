#!/usr/bin/env php
<?php
// === Defines para evitar bloqueos ===
define('NOREQUIREMENU',    1);
define('NOLOGIN',          1);
define('NOREQUIREHTML',    1);
define('NOTOKENRENEWAL',   1);
define('NOCSRFCHECK',      1);
define('NOSCANPOSTFORINJECTION', 1);
define('NOSCANGETFORINJECTION',  1);

// Simular GET
$_SERVER['REQUEST_METHOD']  = 'GET';
$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF']        = '/custom/picaje/scripts/test_resumen_pdf.php';

// Carga Dolibarr
require_once dirname(__DIR__, 3) . '/main.inc.php';
// Carga la librería de PDF
require_once DOL_DOCUMENT_ROOT . '/core/lib/pdf.lib.php';
// Carga tu función de resumen
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/export/resumen_mensual.php';

// Preparar datos de ejemplo
$today = new DateTime();
$start = $today->format('Y-m-d');
$end   = $start;
$dias  = [
    $start => [
        'entrada' => '09:00',
        'salida'  => '17:30',
    ]
];
$totalHoras         = 8.5;
$horasExtras        = 0.5;
$salidasAnticipadas = 0;

// Seleccionar un usuario de prueba (activo)
$res = $db->query("SELECT rowid FROM " . MAIN_DB_PREFIX . "user WHERE statut = 1 LIMIT 1");
$obj = $db->fetch_object($res);
$userObj = new User($db);
$userObj->fetch($obj->rowid);

// Cabeceras para PDF inline
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="resumen_test.pdf"');

// Generar y guardar PDF en archivo temporal
$testPdfPath = DOL_DOCUMENT_ROOT . '/documents/resumenes/test_resumen.pdf';
dol_mkdir(dirname($testPdfPath));
generarResumenMensualPDF(
    $db,
    $userObj,
    $dias,
    $totalHoras,
    $horasExtras,
    $salidasAnticipadas,
    $start,
    $end,
    $testPdfPath
);

// Enviar PDF al navegador
if (file_exists($testPdfPath)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="test_resumen.pdf"');
    readfile($testPdfPath);
    exit;
} else {
    echo "❌ Error: No se generó el PDF de prueba en $testPdfPath";
    exit;
}
