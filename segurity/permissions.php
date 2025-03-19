<?php
// Acceso solo a usuarios con permisos administrativos (o permisos específicos para el módulo)
if ($user->rights->mimodulo->admin) {
    // El usuario tiene permisos para acceder a la administración del módulo
    return 1;
} else {
    // El usuario no tiene permisos, redirigir a la página de error o mostrar un mensaje
    dol_print_error('', 'No tienes permisos para acceder a este módulo.');
    exit;
}
?>
