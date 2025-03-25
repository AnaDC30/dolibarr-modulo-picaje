<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
session_start();

global $db, $user;

// Validar el token CSRF
$token_post = GETPOST('token', 'alpha');
if (empty($token_post) || $token_post !== $_SESSION['newtoken']) {
    setEventMessages("❌ Error de seguridad: Token CSRF no válido.", null, 'errors');
    header("Location: ../../tpl/picaje.php");
    exit;
}

// Obtener el tipo de picaje
$tipo = GETPOST('tipo', 'alpha');
if (!in_array($tipo, ['entrada', 'salida'])) {
    setEventMessages("❌ Tipo de picaje no válido", null, 'errors');
    header("Location: ../../tpl/picaje.php");
    exit;
}

// Datos comunes
$fecha = date('Y-m-d');
$hora = date('H:i:s');
$usuario_id = $user->id;

// Obtener geolocalización (puede llegar vacía)
$latitud = isset($_POST['latitud']) && $_POST['latitud'] !== '' ? $_POST['latitud'] : null;
$longitud = isset($_POST['longitud']) && $_POST['longitud'] !== '' ? $_POST['longitud'] : null;

// Preparar consulta con latitud y longitud
$sql = "INSERT INTO llx_picaje (fecha, hora, tipo, usuario_id, latitud, longitud)
        VALUES (
            '" . $db->escape($fecha) . "',
            '" . $db->escape($hora) . "',
            '" . $db->escape($tipo) . "',
            " . (int) $usuario_id . ",
            " . ($latitud !== null ? "'" . $db->escape($latitud) . "'" : "NULL") . ",
            " . ($longitud !== null ? "'" . $db->escape($longitud) . "'" : "NULL") . "
        )";

$res = $db->query($sql);

if ($res) {
    setEventMessages("✅ Picaje de $tipo registrado correctamente.", null, 'mesgs');
} else {
    setEventMessages("❌ Error al registrar el picaje: " . $db->lasterror(), null, 'errors');
}

echo "✅ Picaje registrado correctamente. Redirigiendo...";
header("Refresh: 3; URL=../../tpl/picaje.php");
exit;
?>

