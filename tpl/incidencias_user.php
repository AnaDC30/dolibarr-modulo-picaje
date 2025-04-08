<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

if ($user->admin) {
    echo json_encode(['success' => false, 'error' => 'Solo usuarios pueden enviar incidencias.']);
    exit;
}

echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/modal.css', 1) . '">';
echo '<link rel="stylesheet" href="' . dol_buildpath('/custom/picaje/css/incidencias.css', 1) . '">';

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
<header class="page-header">
    <h2>Incidencias</h2>
</header>

<?php if (!$user->admin): ?>
        <button class="btn-historial" onclick="document.getElementById('modal-nueva-incidencia').style.display='flex'">
            📩 Reportar incidencia
        </button>
    <?php endif; ?>

<!-- =================== -->
<!--    TABLA LISTADO     -->
<!-- =================== -->
<div class="main-content" style="max-width: 900px; margin: auto;">

  <table class="liste">
    <tr>
      <th>Fecha</th>
      <th>Hora</th>
      <th>Tipo</th>
      <th>Comentario</th>
      <th>Status</th>
    </tr>

    <?php
    if ($res && $db->num_rows($res)) {
        while ($obj = $db->fetch_object($res)) {
            echo '<tr>';
            echo '<td>' . dol_print_date(dol_stringtotime($obj->fecha), 'day') . '</td>';
            echo '<td>' . substr($obj->hora, 0, 5) . '</td>';
            echo '<td>' . ucfirst($obj->tipo) . '</td>';
            echo '<td>' . dol_escape_htmltag($obj->justificacion) . '</td>';
            echo '<td><span class="status-btn ' . strtolower($obj->status) . '">' . $obj->status . '</span></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5" class="center">No has registrado ninguna incidencia.</td></tr>';
    }
    ?>
  </table>
</div>

<!-- MODAL NUEVA INCIDENCIA -->
<div id="modal-nueva-incidencia" class="modal-overlay" style="display: none;">
  <div class="modal-content">
    <div class="modal-inner-form">
      <button class="cerrarModal" onclick="cerrarModalNuevaIncidencia()">×</button>
      <h2>Reportar nueva incidencia</h2>
      <form id="form-nueva-incidencia">
        <label for="tipo">Tipo de incidencia:</label>
        <select name="tipo" id="tipo" required>
          <option value="salida_anticipada">Salida anticipada</option>
          <option value="olvido_picaje">Olvido de picaje</option>
          <option value="otro">Otro</option>
        </select>

        <label for="comentario">Comentario:</label>
        <textarea name="comentario" id="comentario" rows="4" required></textarea>

        <div class="modal-actions">
          <button type="submit" class="guardarButton">Enviar incidencia</button>
          <button type="button" onclick="cerrarModalNuevaIncidencia()">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS -->
<script src="<?php echo dol_buildpath('/custom/picaje/js/picaje.js', 1); ?>"></script>


<!-- =================== -->
<!--    BOTÓN VOLVER     -->
<!-- =================== -->

<div class="backContainer">
    <a href="<?php echo dol_buildpath('/custom/picaje/picajeindex.php', 1); ?>" class="backArrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-big-left-icon lucide-arrow-big-left">
            <path d="M18 15h-6v4l-7-7 7-7v4h6v6z"/>
        </svg>
    </a>
</div>