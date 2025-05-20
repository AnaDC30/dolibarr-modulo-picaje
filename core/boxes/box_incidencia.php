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

    public function loadBox($max = 5) {
        global $langs, $user, $db;
        $langs->load("main");

        $cssLink = '<link rel="stylesheet" type="text/css" href="' . dol_buildpath('/custom/picaje/css/panel.css', 1) . '">';
        $cssLink = '<link rel="stylesheet" type="text/css" href="'.dol_buildpath('/custom/picaje/css/panel.css', 1).'">';
        print $cssLink;

        $this->info_box_head = [
            'text' => $langs->trans("📌 Incidencias Pendientes")
        ];

        $sql = "SELECT fecha, tipo, comentario, fk_user 
                FROM llx_picaje_incidencias 
                WHERE status = 'pendiente'";

        if (!$user->admin) {
            $sql .= " AND fk_user = " . (int)$user->id;
        }

        $sql .= " ORDER BY fecha DESC LIMIT " . (int)$max;

        $resql = $db->query($sql);

        $html = '<div class="boxcontent">';
        $html .= '<table class="noborder allwidth">';
        $html .= '<thead class="liste_titre">';
        $html .= '</thead>';
        $html .= '<tbody>';

        if ($resql && $db->num_rows($resql)) {
            while ($obj = $db->fetch_object($resql)) {
                $fecha = dol_print_date(dol_stringtotime($obj->fecha), 'day');
                $comentario = dol_escape_htmltag($obj->comentario);

                $html .= '<tr class="oddeven">';
                $html .= '<td>' . $comentario . '</td>';

                if ($user->admin) {
                    require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';
                    $usuarioObj = new User($db);
                    $nombreUsuario = '';
                    if ($usuarioObj->fetch($obj->fk_user) > 0) {
                        $nombreUsuario = $usuarioObj->getFullName($langs);
                    }
                    $html .= '<td>👤' . dol_escape_htmltag($nombreUsuario) . '</td>';
                }

                $html .= '<td class="right">' . $fecha . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="' . ($user->admin ? 3 : 2) . '" class="opacitymedium center">';
            $html .= '✅ No hay incidencias pendientes.</td></tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $url = $user->admin
            ? dol_buildpath('/custom/picaje/picajeindex.php?view=incidencias', 1)
            : dol_buildpath('/custom/picaje/picajeindex.php?view=incidencias_user', 1);

        $html .= '<div class="center" style="margin-top: 8px;">';
        $html .= '<a class="button small" href="' . $url . '">📝 Ver todas las incidencias</a>';
        $html .= '</div>';
        $html .= '</div>';

        $this->info_box_contents[0][0] = [
            'tr' => 'class="nohover center"',
            'td' => '',
            'text' => $html
        ];
    }

    public function showBox($head = null, $contents = null, $nooutput = 0) {
        return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
    }
}