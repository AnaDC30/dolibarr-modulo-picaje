<?php
// Cargar el entorno de Dolibarr
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php'; 
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Obtener la URL base del módulo
$moduleUrl = DOL_URL_ROOT . '/custom/mimodulo';

// Incluir archivos CSS globales del módulo
print '<link rel="stylesheet" type="text/css" href="../css/layout.css">';
print '<link rel="stylesheet" type="text/css" href="../css/buttons.css">';
print '<link rel="stylesheet" type="text/css" href="../css/table.css">';

?>
