<?php
// Cargar el entorno de Dolibarr y estilos
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/includes/header.php';

llxHeader("", "Historial de Picaje", "");

// Simulación de registros (luego se extraerán de la base de datos)
$registros = [
    ['fecha' => '2024-03-01', 'hora' => '08:30 AM', 'tipo' => 'Entrada'],
    ['fecha' => '2024-03-01', 'hora' => '17:00 PM', 'tipo' => 'Salida'],
    ['fecha' => '2024-04-02', 'hora' => '08:45 AM', 'tipo' => 'Entrada'],
    ['fecha' => '2024-04-02', 'hora' => '17:15 PM', 'tipo' => 'Salida'],
];

$current_month = null; // Variable para controlar el mes actual
?>

<header class="page-header">
    <h1>Historial de Picaje</h1>
</header>

<!-- Tabla -->
    <table class="customTable">
        <tr>
            <th>Tipo</th>
            <th>Hora</th>
            <th>Fecha</th>
        </tr>

        <?php foreach ($registros as $registro): ?>
            <?php 
            // Obtener el mes del registro actual
            $mes_actual = date("F Y", strtotime($registro['fecha']));

            // Si cambia de mes, agregar un separador en la tabla
            if ($mes_actual !== $current_month): 
                $current_month = $mes_actual;
            ?>
                <tr class="monthSeparator">
                    <td colspan="3"></td>
                </tr>
            <?php endif; ?>

            <tr>
                <td><?php echo $registro['tipo']; ?></td>
                <td><?php echo $registro['hora']; ?></td>
                <td><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>


<!-- Flecha de volver -->
<div class="backContainer">
    <a href="principal.php" class="backArrow">&#8592;</a>
</div>

<?php
llxFooter();
?>
