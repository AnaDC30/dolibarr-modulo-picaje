<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/includes/header.php';

llxHeader("", "Historial de Picaje", "");

global $db;

// Consultar los registros de picaje desde la BD
$sql = "SELECT fecha, hora, tipo FROM llx_picaje ORDER BY fecha DESC, hora DESC";
$resql = $db->query($sql);

$registros = [];
if ($resql) {
    while ($row = $db->fetch_object($resql)) {
        $registros[] = [
            'fecha' => $row->fecha,
            'hora' => $row->hora,
            'tipo' => ucfirst($row->tipo)
        ];
    }
}

$current_month = null;
?>

<header class="page-header">
    <h1>Historial de Picaje</h1>
</header>

    <table class="customTable">
        <tr>
            <th>Tipo</th>
            <th>Hora</th>
            <th>Fecha</th>
            
        </tr>

        <?php foreach ($registros as $registro): ?>
            <?php 
            $mes_actual = date("F Y", strtotime($registro['fecha']));
            if ($mes_actual !== $current_month): 
                $current_month = $mes_actual;
            ?>
                <tr class="monthSeparator">
                    <td colspan="3"><?php echo $current_month; ?></td>
                </tr>
            <?php endif; ?>

            <tr>
                <td><?php echo $registro['tipo']; ?></td>
                <td><?php echo $registro['hora']; ?></td>
                <td><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></td>
                
            </tr>
        <?php endforeach; ?>
    </table>


<div class="backContainer">
    <a href="principal.php" class="backArrow">&#8592;</a>
</div>

<?php
llxFooter();
?>

