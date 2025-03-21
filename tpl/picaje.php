<?php
// =====================
//  CARGA DEL ENTORNO
// =====================

// Cargar entorno de Dolibarr
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

// Cargar lógica del módulo
require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

// Cargar el estilo específico para esta vista
echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/custom/mimodulo/css/picaje.css">';

// Cabecera de Dolibarr
llxHeader("", "Picaje de Trabajadores", "");

// =====================
//  DATOS NECESARIOS
// =====================

// Obtener los registros del día desde la base de datos
$registros = obtenerRegistrosDiarios();

// Obtener el token CSRF de Dolibarr
$token = $_SESSION['newtoken'];
?>

<!-- ===================== -->
<!--   ENCABEZADO VISUAL   -->
<!-- ===================== -->
<header class="page-header">
    <h1>Registro de Picaje</h1>
</header>

<!-- ===================== -->
<!--  CONTENEDORES FLEX    -->
<!-- ===================== -->
<div class="container-flex">

    <!-- === BLOQUE DE ACCIÓN === -->
    <div class="main-content">
        <h2>Marcar Picaje</h2>
        <form method="post" action="../core/modules/procesar_picaje.php">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <button type="submit" name="tipo" value="entrada" class="picajeButton entrada">Entrada</button>
            <button type="submit" name="tipo" value="salida" class="picajeButton salida">Salida</button>
        </form>
    </div>

    <!-- === BLOQUE DE RESULTADO === -->
    <div class="main-content">
        <h2>Registro Diario (<?php echo date("d/m/Y"); ?>)</h2>
        <table class="customTable">
            <tr>
                <th>Hora</th>
                <th>Tipo</th>
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

<!-- ===================== -->
<!--     BOTÓN VOLVER      -->
<!-- ===================== -->
<div class="picajeBack">
    <a href="principal.php" class="picajeArrow">&#8592;</a>
</div>

<?php
// =====================
//    PIE DE PÁGINA
// =====================
llxFooter();
?>


