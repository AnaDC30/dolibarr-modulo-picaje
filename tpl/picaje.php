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

// =============================
// Verificar si el usuario ya ha picado hoy
// =============================
$ha_entrada = false;
$ha_salida = false;

foreach ($registros as $r) {
    if ($r['tipo'] === 'entrada') $ha_entrada = true;
    if ($r['tipo'] === 'salida') $ha_salida = true;
}


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

<!-- === BLOQUE DE ACCIÓN === -->
    <div class="main-content">
        <h2>Registro de Picaje</h2>

        <?php if ($ha_entrada && $ha_salida): ?>
            <!-- Ya se ha picado entrada y salida hoy -->
            <p>✅ Ya has registrado entrada y salida hoy.</p>
        <?php else: ?>
            <!-- Formulario de picaje único -->
            <form method="post" action="../core/modules/procesar_picaje.php" id="form-picaje">
                <!-- Token CSRF -->
                <input type="hidden" name="token" value="<?php echo $token; ?>">

                <!-- Campo oculto para determinar tipo: entrada o salida -->
                <input type="hidden" name="tipo" id="tipo_picaje">

                <!-- Ubicación -->
                <input type="hidden" name="latitud" id="latitud">
                <input type="hidden" name="longitud" id="longitud">

                <!-- Botón único -->
                <button type="submit" class="picajeButton" id="boton-picar">📍 Picar</button>
            </form>
        <?php endif; ?>
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

<!-- ========== MODAL JUSTIFICACIÓN ========== -->
<div id="modalJustificacion" style="display: none;" class="modal-justificacion">
    <div class="modal-contenido">
        <h3>✏️ Justificación de salida anticipada</h3>
        <p>Tu hora de salida prevista aún no ha llegado. Indica el motivo por el cual deseas registrar la salida:</p>
        <textarea id="textoJustificacion" placeholder="Escribe aquí tu motivo..." rows="4"></textarea>
        <div class="modal-buttons">
            <button type="button" onclick="enviarJustificacion()">✅ Confirmar</button>
            <button type="button" onclick="cerrarModalJustificacion()">❌ Cancelar</button>
        </div>
    </div>
</div>

<!-- Campo oculto para pasar la justificación al backend -->
<input type="hidden" name="justificacion" id="inputJustificacion">



<!-- ========================= -->
<!--  UBICACIÓN Y TIPO PICAJE  -->
<!-- ========================= -->

<script>
window.addEventListener('DOMContentLoaded', function () {
    // Elementos del formulario
    const form = document.getElementById('form-picaje');
    const latInput = document.getElementById("latitud");
    const lonInput = document.getElementById("longitud");
    const tipoInput = document.getElementById("tipo_picaje");

    // Estado del día recibido desde PHP
    const haEntrada = <?php echo $ha_entrada ? 'true' : 'false'; ?>;
    const haSalida = <?php echo $ha_salida ? 'true' : 'false'; ?>;

    // Bandera de ubicación obtenida
    let ubicacionObtenida = false;

    // === Obtener la ubicación del navegador ===
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            latInput.value = position.coords.latitude;
            lonInput.value = position.coords.longitude;
            ubicacionObtenida = true;
        }, function (error) {
            alert("❌ No se pudo obtener la ubicación. No podrás picar sin permitir la localización.");
        });
    } else {
        alert("⚠️ Este navegador no soporta geolocalización.");
    }

    // === Evento al enviar el formulario ===
    form.addEventListener('submit', function (e) {
        if (!latInput.value || !lonInput.value || !ubicacionObtenida) {
            e.preventDefault();
            alert("❌ No se ha detectado la ubicación. No se puede registrar el picaje.");
            return;
        }

        // Lógica de picaje inteligente
        if (!haEntrada) {
            tipoInput.value = 'entrada';
        } else if (haEntrada && !haSalida) {
            e.preventDefault(); // Evitar el envío automático

            // Llamada al backend para saber si la salida es anticipada
            fetch(`../core/modules/validar_salida.php`)
                .then(response => response.json())
                .then(data => {
                    if (data.salida_anticipada) {
                        // Mostrar modal de justificación
                        document.getElementById('modalJustificacion').style.display = 'flex';
                    } else {
                        // Salida normal, enviar directamente
                        tipoInput.value = 'salida';
                        form.submit();
                    }
                })
                .catch(err => {
                    alert("❌ Error al validar hora de salida.");
                console.error(err);
                });
        }
    });
});
</script>


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


