<?php
// Evita ejecución de acciones por defecto de Dolibarr
$_GET['action'] = '';
$_POST['action'] = '';

// Requisitos básicos
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';

$langs->load("picaje@picaje");

// Cabecera de Dolibarr
$page_name = "PanelPicaje";
llxHeader('', $langs->trans($page_name));
echo '<link rel="stylesheet" type="text/css" href="'.dol_buildpath('/custom/picaje/css/panel.css', 1).'">';

// === Lógica para determinar el siguiente tipo de picaje ===
$sql = "SELECT tipo FROM llx_picaje 
        WHERE fk_user = " . (int) $user->id . " 
        AND DATE(fecha_hora) = '" . $db->escape(date('Y-m-d')) . "' 
        ORDER BY fecha_hora ASC";
$resql = $db->query($sql);

$tipos = [];
if ($resql) {
	while ($obj = $db->fetch_object($resql)) {
		$tipos[] = $obj->tipo;
	}
}
$entradas = count(array_filter($tipos, fn($t) => $t === 'entrada'));
$salidas  = count(array_filter($tipos, fn($t) => $t === 'salida'));
$siguiente = ($entradas > $salidas) ? 'salida' : 'entrada';
$claseBtn = 'picajeButton ' . $siguiente;
$textoBtn = $langs->trans("Picar " . $siguiente);

// Contenido del panel
echo '<div id="picaje-panel">';
echo '<h2>' . $langs->trans("Bienvenido al Panel de Picaje") . '</h2>';
echo '<button id="btnPicajePanel" class="' . $claseBtn . '">' . $textoBtn . '</button>';
echo '<div id="toast" class="boxToastStyle" style="display:none; margin-top: 20px;"></div>';
echo '</div>';

// Pie de página
llxFooter();
?>

<!-- Script para manejar el botón de picaje y mostrar el toast -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const boton = document.getElementById('btnPicajePanel');
    const toast = document.getElementById('toast');

    boton.addEventListener('click', function () {
        if (!navigator.geolocation) {
            mostrarToast("⚠️ Geolocalización no soportada", false);
            return;
        }

        navigator.geolocation.getCurrentPosition(function (position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            fetch('<?php echo dol_buildpath("/custom/picaje/ajax/picar_desde_panel.php", 1); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ latitud: lat, longitud: lon })
            })
            .then(response => response.json())
            .then(data => {
                if (data.anticipada) {
                    document.getElementById('modalJustificacion').style.display = 'flex';
                    return;
                }

             mostrarToast(data.mensaje, data.exito);

                // Cambiar estilo del botón según siguiente picada
                if (data.siguiente === 'entrada') {
                    boton.classList.remove('salida');
                    boton.classList.add('entrada');
                    boton.textContent = 'Picar entrada';
                } else {
                    boton.classList.remove('entrada');
                    boton.classList.add('salida');
                    boton.textContent = 'Picar salida';
                }
            })
        .catch(() => {
            mostrarToast("❌ Error en la petición", false);
        });
        
    });

    function mostrarToast(mensaje, exito) {
        toast.textContent = mensaje;
        toast.style.backgroundColor = exito ? '#28a745' : '#dc3545';
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }
});
</script>
