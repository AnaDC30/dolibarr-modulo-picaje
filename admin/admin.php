<?php
// ==============================
// CARGA DEL ENTORNO DOLIBARR
// ==============================
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

// ==============================
// SEGURIDAD: solo admin/root
// ==============================
if (!$user->admin) {
    accessforbidden();
}

// ==============================
// CARGA DE IDIOMAS
// ==============================
$langs->load("admin");
$langs->load("mimodulo@admin");

// ==============================
// TÍTULO DE LA PÁGINA
// ==============================
$page_name = "Configuración del Módulo Picaje";

// ==============================
// GUARDADO DE OPCIONES
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    dolibarr_set_const($db, 'PICAR_AUTO_LOGIN', GETPOST('PICAR_AUTO_LOGIN', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'PICAR_MOSTRAR_BOTON_HEADER', GETPOST('PICAR_MOSTRAR_BOTON_HEADER', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'PICAR_SALIDA_AUTOMATICA', GETPOST('PICAR_SALIDA_AUTOMATICA', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'PICAR_SALIDA_MANUAL_JUSTIFICADA', GETPOST('PICAR_SALIDA_MANUAL_JUSTIFICADA', 'int'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'PICAR_MODO_HORARIO', GETPOST('PICAR_MODO_HORARIO', 'alpha'), 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'PICAR_DURACION_JORNADA', GETPOST('PICAR_DURACION_JORNADA', 'int'), 'chaine', 0, '', $conf->entity);

    setEventMessages("✔️ Configuración actualizada correctamente", null, 'mesgs');
}

// ==============================
// CABECERA DOLIBARR + TÍTULO
// ==============================
llxHeader('', $page_name, '');
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($page_name, $linkback, 'title_setup');
?>

<!-- ============================== -->
<!-- FORMULARIO DE CONFIGURACIÓN -->
<!-- ============================== -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <table class="noborder" width="100%">
        <tr class="liste_titre">
            <th colspan="2">Opciones Generales</th>
        </tr>

        <!-- Entrada automática al iniciar sesión -->
        <tr>
            <td>✅ Picaje automático al iniciar sesión (login):</td>
            <td>
                <input type="checkbox" name="PICAR_AUTO_LOGIN" value="1"
                    <?php if (getDolGlobalInt('PICAR_AUTO_LOGIN')) echo 'checked'; ?>>
            </td>
        </tr>

        <!-- Botón en el header de Dolibarr -->
        <tr>
            <td>✅ Mostrar botón "Picar" en el header:</td>
            <td>
                <input type="checkbox" name="PICAR_MOSTRAR_BOTON_HEADER" value="1"
                    <?php if (getDolGlobalInt('PICAR_MOSTRAR_BOTON_HEADER')) echo 'checked'; ?>>
            </td>
        </tr>

        <!-- Activar salida automática -->
        <tr>
            <td>✅ Activar salida automática según horario:</td>
            <td>
                <input type="checkbox" name="PICAR_SALIDA_AUTOMATICA" value="1"
                    <?php if (getDolGlobalInt('PICAR_SALIDA_AUTOMATICA')) echo 'checked'; ?>>
            </td>
        </tr>

        <!-- Permitir salida manual con justificación -->
        <tr>
            <td>✅ Permitir salida manual anticipada con justificación:</td>
            <td>
                <input type="checkbox" name="PICAR_SALIDA_MANUAL_JUSTIFICADA" value="1"
                    <?php if (getDolGlobalInt('PICAR_SALIDA_MANUAL_JUSTIFICADA')) echo 'checked'; ?>>
            </td>
        </tr>

        <!-- Selector de modo de horario -->
        <tr>
            <td>🕒 Modo de horarios:</td>
            <td>
                <select name="PICAR_MODO_HORARIO">
                    <option value="usuario" <?php if (getDolGlobalString('PICAR_MODO_HORARIO') == 'usuario') echo 'selected'; ?>>Por usuario</option>
                    <option value="departamento" <?php if (getDolGlobalString('PICAR_MODO_HORARIO') == 'departamento') echo 'selected'; ?>>Por departamento</option>
                </select>
            </td>
        </tr>

        <!-- Duración de jornada (fallback si no hay horario definido) -->
        <tr>
            <td>⏱️ Duración de jornada predeterminada (en horas):</td>
            <td>
                <input type="number" name="PICAR_DURACION_JORNADA"
                    value="<?php echo getDolGlobalInt('PICAR_DURACION_JORNADA') ?: 8; ?>" min="1" max="24">
            </td>
        </tr>
    </table>

    <div class="center">
        <br><input type="submit" class="button" value="💾 Guardar configuración">
    </div>
</form>

<?php
// ==============================
// PIE DE PÁGINA DOLIBARR
// ==============================
llxFooter();
?>
