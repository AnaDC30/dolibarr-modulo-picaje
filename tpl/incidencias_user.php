<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

if (!$user->id) {
  $response['error'] = 'Debes iniciar sesión para enviar incidencias.';
  echo json_encode($response);
  exit;
}

echo '<link rel="stylesheet" href="' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/style.css.php">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/layout.css', 1) . '">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/modal.css', 1) . '">';

global $db, $user, $conf;

// ===================
// CONSULTA INCIDENCIAS
// ===================
$sql = "SELECT i.*, p.fecha_hora 
        FROM llx_picaje_incidencias i
        LEFT JOIN llx_picaje p ON p.id = i.fk_picaje
        WHERE i.entity = " . (int) $conf->entity . "
        AND i.fk_user = " . (int) $user->id . "
        ORDER BY i.fecha DESC, i.hora DESC";

$res = $db->query($sql);
?>

<!-- =================== -->
<!--    ENCABEZADO       -->
<!-- =================== -->
<div class="titre">
    <span class="inline-block valignmiddle">
        <?php echo img_picto('', 'picaje@picaje'); ?>
    </span>
    <span class="inline-block valignmiddle" style="font-size: 22px; font-weight: bold;">
        <?php echo $langs->trans("Incidencias"); ?>
    </span>
</div>

<!-- Botón para nueva incidencia -->
<?php if (!$user->admin): ?>
    <div style="margin: 20px 0;">
        <button class="ui-button ui-widget ui-state-default ui-corner-all" onclick="document.getElementById('modal-nueva-incidencia').style.display='flex'">
            📩 Reportar incidencia
        </button>
    </div>
<?php endif; ?>

<!-- =================== -->
<!--    TABLA LISTADO     -->
<!-- =================== -->
<div class="div-table-responsive">
    <table class="noborder allwidth">
        <thead class="liste_titre">
    <tr>
        <th class="center">Fecha</th>
        <th class="center">Hora</th>
        <th class="center">Tipo</th>
        <th class="center">Comentario</th>
        <th class="center">Estado</th>
        <th class="center">Resolución</th>
    </tr>
</thead>
<tbody>
    <?php
    if ($res && $db->num_rows($res)) {
        while ($obj = $db->fetch_object($res)) {
            $tipo_legible = match ($obj->tipo) {
                'salida_anticipada' => 'Salida anticipada',
                'horas_extra' => 'Horas extra',
                'olvido_picaje' => 'Olvido de picaje',
                'otro' => 'Otro',
                default => ucfirst($obj->tipo)
            };

            $estado = strtolower($obj->status);
            echo '<tr class="oddeven fila-dinamica">';
            echo '<td class="center">' . dol_print_date(dol_stringtotime($obj->fecha), 'day') . '</td>';
            echo '<td class="center">' . substr($obj->hora, 0, 5) . '</td>';
            echo '<td class="center">' . dol_escape_htmltag($tipo_legible) . '</td>';
            echo '<td class="center">' . dol_escape_htmltag($obj->comentario) . '</td>';
            echo '<td class="center"><span class="status-btn ' . $estado . '">' . ucfirst($estado) . '</span></td>';
            echo '<td class="center">' . (!empty($obj->resolucion) && $estado === 'resuelta' ? dol_escape_htmltag($obj->resolucion) : '-') . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr class="oddeven"><td colspan="6" class="center opacitymedium">No has registrado ninguna incidencia.</td></tr>';
    }
    ?>
</tbody>

  </table>
  <div class="center" style="margin-top: 10px;">
    <button id="btn-mostrar-mas" class="button small">Mostrar más</button>
   </div>
</div>

<!-- MODAL NUEVA INCIDENCIA -->
<?php include_once dol_buildpath('/custom/picaje/tpl/modales.php', 0); ?>


<!-- JS -->
<script src="<?php echo dol_buildpath('/custom/picaje/js/picaje.js', 1); ?>"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const filas = document.querySelectorAll(".fila-dinamica");
  const btn = document.getElementById("btn-mostrar-mas");
  const batchSize = 10;
  let mostradas = 0;

  function mostrarMas() {
    for (let i = mostradas; i < mostradas + batchSize && i < filas.length; i++) {
      filas[i].style.display = "";
    }
    mostradas += batchSize;
    if (mostradas >= filas.length && btn) {
      btn.style.display = "none";
    }
  }

  filas.forEach((fila, i) => {
    fila.style.display = i < batchSize ? "" : "none";
  });

  if (btn) btn.addEventListener("click", mostrarMas);
});
</script>
