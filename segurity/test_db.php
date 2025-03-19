<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';

global $db;

if ($db->connected) {
    echo "✅ Conexión exitosa a la base de datos de Dolibarr.";
} else {
    echo "❌ Error en la conexión a la base de datos.";
}
?>
