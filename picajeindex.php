<?php

// Carga del entorno Dolibarr
$res = 0;
if (!$res && file_exists("../main.inc.php")) {
    $res = include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
    $res = include "../../main.inc.php";
}
if (!$res) {
    die("Include of main fails");
}

// Cargar archivos necesarios
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

// Cargar idiomas del módulo
$langs->loadLangs(array("picaje@picaje"));



// Título y cabecera estándar
llxHeader("", $langs->trans("PicajeArea"), '', '', 0, 0, '', '', '', 'mod-picaje page-index');

// Controlador de vistas internas
$view = GETPOST('view', 'alpha');
switch ($view) {
    case 'historial':
        include dol_buildpath('/custom/picaje/tpl/historial.php', 0);
        break;
    case 'picaje':
        include dol_buildpath('/custom/picaje/tpl/picaje.php', 0);
        break;
    case 'editar_usuario':
        include dol_buildpath('/custom/picaje/tpl/editar_horario_usuario.php', 0);
        break;
    case 'editar_grupo':
        include dol_buildpath('/custom/picaje/tpl/editar_horario_grupo.php', 0);
        break;
    case 'log_modificaciones':
        include dol_buildpath('/custom/picaje/tpl/log_modificaciones.php', 0);
        break;
    default:
        include dol_buildpath('/custom/picaje/tpl/principal.php', 0);
}

// Pie de página estándar Dolibarr
llxFooter();
