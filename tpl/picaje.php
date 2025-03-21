<?php
// Cargar el entorno de Dolibarr y estilos
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

// Enlazar el CSS específico de esta vista
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/custom/mimodulo/css/picaje.css">';

session_start();

// Mostrar cabecera estándar de Dolibarr
llxHeader("", "Registro de Picaje", "");

// Obtener registros desde la base de datos
$registros = obtenerRegistrosDiarios();

// Obtener el token CSRF de Dolibarr
$token = $_SESSION['newtoken'];
?>

<header class="page-header">
    <h1>Registro de Picaje</h1>
</header>

<!-- Contenedor principal con diseño flexible -->
<div class="container-flex">

    <!-- Sección de botones de entrada/salida -->
    <div class="main-content">
        <p>Selecciona el tipo de picaje:</p>
        <form method="post" action="../core/modules/procesar_picaje.php">
            <input type="hidden" name="token" value="<?php echo $token; ?>"> <!-- Protección CSRF -->
            <button type="submit" name="tipo" value="entrada" class="customButton">Marcar Entrada</button>
            <button type="submit" name="tipo" value="salida" class="customButton">Marcar Salida</button>
        </form>
    </div>

    <!-- Sección del registro diario -->
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
                        <td><?php echo ucfirst($registro['tipo']); ?></td>
                        <td><?php echo $registro['hora']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

</div>

<!-- Botón volver -->
<div class="backContainer">
    <a href="principal.php" class="backArrow">&#8592;</a>
</div>

<?php
// Mostrar pie estándar de Dolibarr
llxFooter();
?>

