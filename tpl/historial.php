<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

// Enlazar el CSS específico de esta vista
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/custom/mimodulo/css/historial.css">';

llxHeader("", "Historial de Picaje", "");

// Obtener historial desde la base de datos
$historial = obtenerHistorialPicajes();
?>

<header class="page-header">
    <h1>Historial de Picaje</h1>
</header>

<div class="table-container">
    <div class="table-wrapper position-relative">
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
                    <?php foreach ($historial as $index => $registro): ?>
                        <tr class="table-row" data-index="<?php echo $index; ?>">
                            <td><?php echo $registro['tipo']; ?></td>
                            <td><?php echo $registro['hora']; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($registro['fecha'])); ?></td>
                        </tr>

                        <?php if ($user->admin == 1): ?>
                            <div class="floating-buttons" data-index="<?php echo $index; ?>">
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




