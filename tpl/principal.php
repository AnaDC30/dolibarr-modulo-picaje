<?php
// Cargar el archivo principal de Dolibarr
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php'; 
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php'; // Cargar las librerías de administración de Dolibarr
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/includes/header.php'; //Carga los estilos de header.php

llxHeader(); // Encabezado de Dolibarr

?>

<header class="page-header">
    <h1>Bienvenido al Módulo</h1>
</header>

<!-- Contenedor padre que alinea los elementos en fila -->
<div class="container-flex">
    <!-- Contenedor para realizar el picaje -->
    <div class="main-content">
        <p>Realizar Picaje</p>
        <a class="customButton" href="picaje.php">Picar</a>
    </div>

    <!-- Contenedor para ver el registro de picadas -->
    <div class="main-content">
        <p>Consultar el historial de picadas realizadas</p>
        <a class="customButton" href="historial.php">Ver Registro</a>
    </div>
</div>

<?php

llxFooter();
?>
