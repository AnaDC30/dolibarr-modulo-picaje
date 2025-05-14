<?php

include_once DOL_DOCUMENT_ROOT . '/core/boxes/modules_boxes.php';

class box_incidencia extends ModeleBoxes {
    public $boxcode = "incidencia";
    public $boximg = "object_generic";
    public $boxlabel = "Incidencias";
    public $depends = ['picaje'];
    public $version = 'dolibarr';

    public function __construct($db, $param = '') {
        global $langs;
        $langs->load("picaje@picaje");
        $this->db = $db;

    }

    public function loadBox($max = 5)
    {
        global $langs, $user, $db;
        $langs->load("main");

        $cssLink = '<link rel="stylesheet" type="text/css" href="'.dol_buildpath('/custom/picaje/css/panel.css', 1).'">';
        $html = $cssLink;
        $html .= '<ul class="incidencia-box-list">';

        $this->info_box_head = array(
            'text' => $langs->trans("📌 Incidencias Pendientes"),
        );

        $sql = "SELECT fecha, tipo, comentario, fk_user 
                FROM llx_picaje_incidencias 
                WHERE status = 'pendiente'";

        if (!$user->admin) {
            $sql .= " AND fk_user = " . (int)$user->id;
        }

        $sql .= " ORDER BY fecha DESC LIMIT " . (int)$max;

        $resql = $db->query($sql);

        if ($resql && $db->num_rows($resql)) {
            while ($obj = $db->fetch_object($resql)) {
                $fecha = dol_print_date(dol_stringtotime($obj->fecha), 'day'); 
                $linea = '🔴 '. dol_escape_htmltag($obj->comentario) . ' - ' . $fecha;

                if ($user->admin) {
                    require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
                    $usuarioObj = new User($db);
                    if ($usuarioObj->fetch($obj->fk_user) > 0) {
                        $nombreUsuario = $usuarioObj->getFullName($langs);
                        $linea .= ' - <small>👤 ' . dol_escape_htmltag($nombreUsuario) . '</small>';
                    }
                }

                $html .= '<li>' . $linea . '</li>';
            }
        } else {
            $html .= '<div>✅ No hay incidencias pendientes.</div>';
        }

        $html .= '<div class="incidencias-vermas">';
        $url = $user->admin
            ? dol_buildpath('/custom/picaje/picajeindex.php?view=incidencias', 1)
            : dol_buildpath('/custom/picaje/picajeindex.php?view=incidencias_user', 1);

        $html .= '<div class="center"><a class="button small" href="' . $url . '">📝 Ver todas las incidencias</a></div>';

        $html .= '</div></div>';

        $this->info_box_contents[0][0] = array(
            'tr' => 'class="center"',
            'td' => '',
            'text' => $html,
            'asis' => 1
        );
    }

    public function showBox($head = null, $contents = null, $nooutput = 0) {
        return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
    }
}
