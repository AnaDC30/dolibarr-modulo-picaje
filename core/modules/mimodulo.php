<?php
// Cargar el archivo principal de Dolibarr
require '../main.inc.php'; 
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php'; // Cargar las librerías de administración de Dolibarr

// Redirigir directamente a la vista de bienvenida
header("Location: " . DOL_URL_ROOT . "/custom/mimodulo/tpl/principal.php");
exit;
?>

