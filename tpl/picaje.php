<?php
// Cargar el entorno de Dolibarr y estilos
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/includes/header.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

session_start();

llxHeader("", "Registro de Picaje", "");

// Obtener registros desde la BBDD
$registros = obtenerRegistrosDiarios();

// Obtener el token CSRF de Dolibarr
$token = $_SESSION['newtoken'];
?>


<header class="page-header">
    <h1>Registro de Picaje</h1>
</header>

<!-- Contenedor principal flexible -->
<div class="container-flex">
    <!-- Contenedor de selección de picaje -->
    <div class="main-content">
        <p>Selecciona el tipo de picaje:</p>
        <form method="post" action="../core/modules/procesar_picaje.php">
            <input type="hidden" name="token" value="<?php echo $token; ?>"> <!-- Token CSRF -->
            <button type="submit" name="tipo" value="entrada" class="customButton">Marcar Entrada</button>
            <button type="submit" name="tipo" value="salida" class="customButton">Marcar Salida</button>
        </form>
    </div>

    <!-- Contenedor de la tabla de registro diario -->
    <div class="main-content">
        <h2>Registro Diario (<?php echo date("d/m/Y"); ?>)</h2>
        <table class="customTable">
            <tr>
                <th>Tipo</th>
                <th>Hora</th>
            </tr>
            <?php if (empty($registros)): ?>
                <tr>
                    <td colspan="2">No hay registros hoy.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?php echo $registro['hora']; ?></td>
                        <td><?php echo ucfirst($registro['tipo']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
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
