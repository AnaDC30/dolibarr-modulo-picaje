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
	$cssLink .= '<link rel="stylesheet" type="text/css" href="'.dol_buildpath('/custom/picaje/css/modal.css', 1).'">';
	$cssLink .= '<script src="'.dol_buildpath('/custom/picaje/js/picaje.js', 1).'"></script>';

	$tokenScript = '<script>const csrfToken = "' . newToken() . '";</script>';


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

	$html = $tokenScript . $cssLink . '
		<button id="btnBoxPicaje" class="'.$claseBtn.'">'.$textoBtn.'</button>
		<div id="boxToast" class="boxToastStyle" style="display:none; margin-top:10px;"></div>
		<div id="modalJustificacion" class="modal-overlay">
			<div class="modal-content">
				<button class="cerrarModal" onclick="cerrarModalJustificacion()">✖</button>
				<div class="modal-inner-form">
				<h2>✏️ Justificación de Picaje anticipado</h2>
				<p>Tu hora de entrada/salida prevista aún no ha llegado. Indica el motivo por el cual deseas registrar el picaje:</p>
				<form onsubmit="event.preventDefault(); enviarJustificacion();">
					<label>Tipo de incidencia:</label>
					<div class="toggle-group">
						<input type="radio" id="opcion_extra" name="tipoIncidencia" value="horas_extra" required hidden>
						<label for="opcion_extra" class="toggle-btn">Horas extra</label>
						
						<input type="radio" id="opcion_entrada_anticipada" name="tipoIncidencia" value="entrada_anticipada" required hidden>
						<label for="opcion_entrada_anticipada" class="toggle-btn">Entrada anticipada</label>

						<input type="radio" id="opcion_anticipada" name="tipoIncidencia" value="salida_anticipada" required hidden>
						<label for="opcion_anticipada" class="toggle-btn">Salida anticipada</label>

						<input type="radio" id="opcion_otro" name="tipoIncidencia" value="otro" required hidden>
						<label for="opcion_otro" class="toggle-btn">Otro</label>
					</div>
			
					<label for="textoJustificacion">Motivo:</label>
						<textarea id="textoJustificacion" placeholder="Escribe aquí tu motivo..." rows="4" required></textarea>

					<div class="modal-actions">
						<button type="button" onclick="cerrarModalJustificacion()">Cancelar</button>
						<button type="submit" class="guardarButton">Confirmar</button>
					</div>
				</form>
			
				</div>
			</div>
			</div>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				const boton = document.getElementById("btnBoxPicaje");
				const toast = document.getElementById("boxToast");

				if (boton) {
					boton.addEventListener("click", function () {
						if (!navigator.geolocation) {
							toast.textContent = "⚠️ Geolocalización no soportada.";
							toast.style.background = "#dc3545";
							toast.style.display = "block";
							setTimeout(() => { toast.style.display = "none"; }, 10000);
							return;
						}

						navigator.geolocation.getCurrentPosition(function (position) {
							const lat = position.coords.latitude;
							const lon = position.coords.longitude;

							fetch("'.dol_buildpath('/custom/picaje/ajax/picar_desde_panel.php', 1).'", {
								method: "POST",
								headers: {
									"Content-Type": "application/json"
								},
								body: JSON.stringify({ latitud: lat, longitud: lon })
							})
							.then(response => response.json())
							.then(data => {
    							if (data.anticipada) {
									const modal = document.getElementById("modalJustificacion");
									if (modal) {
										modal.style.display = "flex";

										// Seleccionar automáticamente el tipo de incidencia según data.tipo
										const tipo = data.tipo;
										if (tipo) {
											const inputRadio = document.querySelector(`input[name="tipoIncidencia"][value="${tipo}_anticipada"]`);
											if (inputRadio) {
												inputRadio.checked = true;
											} else {
												console.warn("⚠ No se encontró el input correspondiente a tipo:", tipo);
											}
										}
									} else {
										console.warn("⚠ No se encontró el modalJustificacion.");
									}
									return;
								}

    							if (toast) {
        							toast.textContent = data.mensaje;
        							toast.style.background = data.exito ? "#28a745" : "#dc3545";
        							toast.style.display = "block";
        							setTimeout(() => { toast.style.display = "none"; }, 10000);
    							}

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
						}, function () {
							toast.textContent = "❌ No se pudo obtener la ubicación.";
							toast.style.background = "#dc3545";
							toast.style.display = "block";
							setTimeout(() => { toast.style.display = "none"; }, 10000);
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
