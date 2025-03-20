<?php
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

class modMiModulo extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs;
        $this->db = $db;

        $this->numero = 600000; // Número único del módulo (elige uno alto para evitar conflictos)
        $this->rights_class = 'mimodulo';
        $this->family = "custom"; // Categoría del módulo
        $this->module_position = 500; // Posición en la lista de módulos
        $this->name = "MiModulo"; // Nombre del módulo
        $this->description = $langs->trans("Este es mi módulo en Dolibarr");
        $this->version = '2'; // Versión del módulo
        
        // Estado del módulo
        $this->special = 0;
        $this->picto= "user";

        // Dependencias del módulo (0 = sin dependencias)
        $this->depends = array();
        $this->required_by = array();
        $this->conflictwith = array();
        $this->phpmin = array(7, 0); // Versión mínima de PHP
        $this->need_dolibarr_version = array(12, 0); // Versión mínima de Dolibarr
        $this->langfiles = array("mimodulo@mimodulo");

        // Configuración de permisos
        $this->rights = array();
        $this->rights[0][0] = 600001; // ID de permiso único
        $this->rights[0][1] = 'Acceso a Mi Módulo';
        $this->rights[0][3] = 1; // Visible en la UI
        $this->rights[0][4] = 'mimodulo'; // Nombre del permiso

        // Definir menús
        $this->menu = array();
        $r = 0;

        // Menú principal
        $this->menu[$r] = array(
            'fk_menu' => 0, // 0 = menú principal
            'type' => 'top',
            'titre' => 'Mi Módulo',
            'mainmenu' => 'mimodulo',
            'url' => '/custom/mimodulo/tpl/principal.php',
            'langs' => 'mimodulo@mimodulo',
            'position' => 100,
            'enabled' => '1',
            'perms' => '1',
            'target' => '',
            'user' => 2
        );
        $r++;

        // Pestañas dentro de fichas de objetos de Dolibarr (opcional)
        $this->tabs = array();

        // Diccionarios (opcional)
        $this->dictionaries = array();
    }
}
?>
