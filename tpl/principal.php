<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php'; 
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

// Enlazar el CSS específico de esta vista
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/custom/mimodulo/css/layout.css">';

llxHeader(); 
?>

<header class="page-header">
    <h1>Bienvenido al Módulo</h1>
</header>

<div class="container-flex">
    <div class="main-content">
        <p>Realizar Picaje</p>
        <a class="mainButton" href="picaje.php">Picar</a>
    </div>

    <div class="main-content">
        <p>Registro de picadas</p>
        <a class="mainButton" href="historial.php">Ver Registro</a>
    </div>
</div>

<?php llxFooter(); ?>
