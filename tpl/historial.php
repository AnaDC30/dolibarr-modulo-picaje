<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Picaje</title>
    <link rel="stylesheet" href="../css/buttons.css">
    <link rel="stylesheet" href="../css/table.css">
</head>
<body>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/includes/header.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

llxHeader("", "Historial de Picaje", "");

// Obtener historial desde la base de datos
$historial = obtenerHistorialPicajes();
?>

<header class="page-header">
    <h1>Historial de Picaje</h1>
</header>

<div class="table-container">
    <div class="table-wrapper">
        <table class="picajeTable">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Hora</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)): ?>
                    <tr>
                        <td colspan="3">No hay registros disponibles.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historial as $registro): ?>
                        <tr class="table-row" data-row-id="<?php echo $registro['id']; ?>">
                            <td><?php echo $registro['tipo']; ?></td>
                            <td><?php echo $registro['hora']; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></td>
                        </tr>

                        <?php if ($user->admin == 1): ?>
                            <div class="floating-buttons" data-row-id="<?php echo $registro['id']; ?>">
                                <form method="post" action="../core/modules/modificar_picaje.php">
                                    <input type="hidden" name="id_picaje" value="<?php echo $registro['id']; ?>">
                                    <button type="submit" name="accion" value="eliminar" class="deleteButton tableButton">❌</button>
                                    <button type="submit" name="accion" value="editar" class="editButton tableButton">✏️</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>




<div class="backContainer">
    <a href="principal.php" class="backArrow">&#8592;</a>
</div>

<?php
llxFooter();
?>




