<?php
// Cargar el entorno de Dolibarr y estilos
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/includes/header.php';

// Simulación de registros de picaje (luego se reemplazará con la base de datos)
$registro_diario = [
    ['tipo' => 'Entrada', 'hora' => '08:30 AM'],
    ['tipo' => 'Salida', 'hora' => '17:00 PM'],
];

llxHeader("", "Registro de Picaje", "");
?>

<header class="page-header">
    <h1>Registro de Picaje</h1>
</header>

<!-- Contenedor principal flexible -->
<div class="container-flex">
    <!-- Contenedor de selección de picaje -->
    <div class="main-content">
        <p>Selecciona el tipo de picaje:</p>
        <form method="post" action="../modules/procesar_picaje.php">
            <button type="submit" name="tipo" value="entrada" class="customButton">Marcar Entrada</button>
            <button type="submit" name="tipo" value="salida" class="customButton">Marcar Salida</button>
        </form>
    </div>

    <!-- Contenedor de la tabla de registro diario -->
    <div class="main-content">
        <h2>Registro Diario</h2>
        <table class="customTable">
            <tr>
                <th>Tipo</th>
                <th>Hora</th>
            </tr>
            <?php foreach ($registro_diario as $registro): ?>
                <tr>
                    <td><?php echo $registro['tipo']; ?></td>
                    <td><?php echo $registro['hora']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<!-- Fecha Volver -->
<div class="backContainer">
    <a href="principal.php" class="backArrow">&#8592;</a>
</div>

<?php
llxFooter();
?>
