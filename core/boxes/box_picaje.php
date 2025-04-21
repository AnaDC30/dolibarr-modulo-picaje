<?php
include_once DOL_DOCUMENT_ROOT."/core/boxes/modules_boxes.php";

class box_picaje extends ModeleBoxes
{
	public $boxcode = "picaje";
	public $boximg = "object_generic";
	public $boxlabel = "Picaje rápido";
	public $depends = array("picaje");
	public $version = '1.0';

	public function __construct($db, $param = '')
	{
		parent::__construct($db, $param);
		$this->param = $param;
	}

	public function loadBox($max = 5)
{
	global $langs, $user, $db;
	$langs->load("main");

	$cssLink = '<link rel="stylesheet" type="text/css" href="'.dol_buildpath('/custom/picaje/css/panel.css', 1).'">';

	// === Obtener estado actual del usuario ===
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

	// === Estilo y texto del botón ===
	$claseBtn = 'picajeButton ' . $siguiente;
	$textoBtn = 'Picar ' . $siguiente;

	$this->info_box_head = array(
		'text' => $langs->trans("Picaje rápido"),
	);

	$html = $cssLink . '
		<button id="btnBoxPicaje" class="'.$claseBtn.'">'.$textoBtn.'</button>
		<div id="boxToast" class="boxToastStyle" style="display:none; margin-top:10px;"></div>

		<script>
			document.addEventListener("DOMContentLoaded", function () {
				const boton = document.getElementById("btnBoxPicaje");
				const toast = document.getElementById("boxToast");

				if (boton) {
					boton.addEventListener("click", function () {
						fetch("'.dol_buildpath('/custom/picaje/ajax/picar_desde_panel.php', 1).'", {
							method: "POST",
							headers: {
								"Content-Type": "application/json"
							},
							body: JSON.stringify({})
						})
						.then(response => response.json())
						.then(data => {
							if (toast) {
								toast.textContent = data.mensaje;
								toast.style.background = data.exito ? "#28a745" : "#dc3545";
								toast.style.display = "block";
								setTimeout(() => { toast.style.display = "none"; }, 4000);
							}

							// Actualizar estilo y texto del botón según siguiente picada
							if (data.siguiente === "entrada") {
								boton.classList.remove("salida");
								boton.classList.add("entrada");
								boton.textContent = "Picar entrada";
							} else {
								boton.classList.remove("entrada");
								boton.classList.add("salida");
								boton.textContent = "Picar salida";
							}
						})
						.catch(() => {
							if (toast) {
								toast.textContent = "Error en la petición.";
								toast.style.background = "#dc3545";
								toast.style.display = "block";
							}
						});
					});
				}
			});
		</script>
	';

	$this->info_box_contents = array();
	$this->info_box_contents[0][0] = array(
		'tr' => 'class="center"',
		'td' => '',
		'text' => $html,
		'asis' => 1
	);
}



	public function showBox($head = null, $contents = null, $nooutput = 0)
	{
		return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
	}
}
