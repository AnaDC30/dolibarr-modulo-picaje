<?php
// === DEFINES para ejecución CLI sin restricciones ===
define('NOREQUIREMENU', 1);
define('NOLOGIN', 1);
define('NOREQUIREHTML', 1);
define('NOTOKENRENEWAL', 1);
define('NOCSRFCHECK', 1);
define('NOSCANPOSTFORINJECTION', 1);
define('NOSCANGETFORINJECTION', 1);
define('EVEN_IF_ONLY_LOGIN_ALLOWED', 1);

// === Simular ejecución por navegador para evitar bloqueos ===
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF'] = '/custom/picaje/scripts/enviar_resumen_mensual.php';

// === Cargar entorno Dolibarr ===
require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/CMailFile.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/user.class.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Fechas del mes anterior
$today = new DateTime();
$start = (clone $today)->modify('first day of last month')->format('Y-m-d');
$end   = (clone $today)->modify('last day of last month')->format('Y-m-d');

// Obtener usuarios con picajes
$sql_users = "SELECT DISTINCT fk_user FROM llx_picaje WHERE DATE(fecha_hora) BETWEEN '$start' AND '$end'";
$res_users = $db->query($sql_users);

while ($res_users && $userRow = $db->fetch_object($res_users)) {
    $user_id = $userRow->fk_user;
    $userObj = new User($db);
    if ($userObj->fetch($user_id) <= 0 || empty($userObj->email)) continue;

    $sql = "SELECT * FROM llx_picaje
            WHERE fk_user = $user_id
            AND DATE(fecha_hora) BETWEEN '$start' AND '$end'
            ORDER BY fecha_hora ASC";
    $res = $db->query($sql);

    $registros = [];
    $horasExtras = 0;
    $salidasAnticipadas = 0;
    $totalHoras = 0;
    $dias = [];

    while ($res && $row = $db->fetch_object($res)) {
        $fecha = date('Y-m-d', strtotime($row->fecha_hora));
        $hora = date('H:i', strtotime($row->fecha_hora));
        $tipo = $row->tipo;

        $dias[$fecha][$tipo] = $hora;
    }

    foreach ($dias as $fecha => $data) {
        if (isset($data['entrada']) && isset($data['salida'])) {
            $entrada = DateTime::createFromFormat('H:i', $data['entrada']);
            $salida = DateTime::createFromFormat('H:i', $data['salida']);
            $diff = $entrada->diff($salida);
            $horas = $diff->h + ($diff->i / 60);
            $totalHoras += $horas;

            if ($horas > 6) $horasExtras += ($horas - 6);
            if ($horas < 6) $salidasAnticipadas++;
        }

        $registros[] = "$fecha: Entrada - " . ($data['entrada'] ?? '---') . ", Salida - " . ($data['salida'] ?? '---');
    }

    // === Crear el PDF ===
    $pdfpath = DOL_DOCUMENT_ROOT . "/documents/resumenes/resumen_{$user_id}_{$start}.pdf";

   


// === URL del PDF generado (para acceso web) ===
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/exports/resumen_mensual.php';
$urlToPdf = DOL_MAIN_URL_ROOT . '/documents/resumenes/' . basename($pdfpath);
dol_mkdir(dirname($pdfpath));

$resumen = new ResumenMensualPDF($userObj, $start, $end, $registros, $totalHoras, $horasExtras, $salidasAnticipadas);
$resumen->generate($pdfpath);

if (!file_exists($pdfpath)) {
    dol_syslog("❌ No se generó el PDF en $pdfpath", LOG_ERR);
    echo "❌ Error: No se creó el PDF en $pdfpath<br>";
    continue;
}

// ✉️ Mensaje plano como respaldo
$mensajePlano = "Hola {$userObj->getFullName($langs)},\n\n"
              . "Tu resumen mensual de picajes ya está disponible.\n"
              . "Puedes descargarlo desde el siguiente enlace:\n"
              . "$urlToPdf\n\n"
              . "Un saludo.";

// Crear el objeto de correo
$mailfile = new CMailFile(
    "Resumen horario de $start a $end",           // Asunto
    $userObj->email,                               // Destinatario
    $conf->global->MAIN_MAIL_EMAIL_FROM,           // Remitente
    $mensajePlano,                                 // Mensaje en texto plano
    array(),                                       // cc
    array(),                                       // adjuntos
    '', '', '', '', 1                              // Otros campos
);


// Enviar el correo
if (!$mailfile->sendfile()) {
    dol_syslog("❌ Error al enviar correo a {$userObj->email}: " . $mailfile->error, LOG_ERR);
    echo "❌ Error al enviar correo: " . $mailfile->error . "<br>";
} else {
    dol_syslog("✅ Correo enviado correctamente a {$userObj->email}", LOG_INFO);
    echo "✅ Correo enviado correctamente a {$userObj->email}<br>";
    
    // Registrar el envío en la base de datos
    $archivo_relativo = "/documents/resumenes/resumen_{$user_id}_{$start}.pdf";

    $sqlInsertLog = "INSERT INTO llx_picaje_resumen_envios (fk_user, fecha_inicio, fecha_fin, archivo_url, fecha_envio)
    VALUES (
        $user_id,
        '".$db->escape($start)."',
        '".$db->escape($end)."',
        '".$db->escape($archivo_relativo)."',
        '".$db->idate(dol_now())."'
    )";

    $db->query($sqlInsertLog);
}

}
?>
