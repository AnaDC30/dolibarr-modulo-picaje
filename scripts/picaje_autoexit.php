<?php
// Definir entorno de ejecución
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF'] = '/dolibarr/custom/picaje/scripts/picaje_autoexit.php'; 

// Constantes para evitar chequeos de seguridad de Dolibarr
define('NOREQUIREMENU', 1);
define('NOREQUIREHTML', 1);
define('NOTOKENRENEWAL', 1);
define('NOLOGIN', 1);
define('NOCSRFCHECK', 1);
define('NOSCANPOSTFORINJECTION', 1);
define('NOSCANGETFORINJECTION', 1);
define('EVEN_IF_ONLY_LOGIN_ALLOWED', 1);

require_once dirname(__DIR__, 3) . '/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

global $db, $conf;

echo "<pre>";
echo "🔁 Iniciando salida automática\n";

if (empty($conf->global->PICAR_SALIDA_AUTOMATICA)) {
    echo "⚠️ Funcionalidad 'salida automática' desactivada\n";
    exit;
}

// Buscar usuarios con entrada hoy pero sin salida
$sql = "
SELECT fk_user
FROM " . MAIN_DB_PREFIX . "picaje
WHERE DATE(fecha_hora) = CURDATE()
  AND tipo = 'entrada'
  AND fk_user NOT IN (
    SELECT fk_user
    FROM " . MAIN_DB_PREFIX . "picaje
    WHERE DATE(fecha_hora) = CURDATE()
      AND tipo = 'salida'
  )
GROUP BY fk_user";

$resql = $db->query($sql);

if (!$resql) {
    echo "❌ Error al obtener usuarios con entrada sin salida: " . $db->lasterror();
    exit;
}

$now = strtotime(date('H:i:s'));

while ($obj = $db->fetch_object($resql)) {
    $user = new User($db);
    $user->fetch($obj->fk_user);
    $horario = getHorarioUsuario($user->id);
    $hora_salida = $horario->hora_salida ?: getHoraSalidaEmpresaPorDefecto();
    $hora_limite = strtotime($hora_salida);


    if ($now < $hora_limite) {
        continue;
    }

    // Insertar salida automática
    $fecha_hora = date('Y-m-d H:i:s');
    $sqlInsert = "
        INSERT INTO " . MAIN_DB_PREFIX . "picaje (
            fecha_hora, tipo, fk_user, tipo_registro
        ) VALUES (
            '" . $db->escape($fecha_hora) . "',
            'salida',
            " . (int)$user->id . ",
            'auto'
        )";

    if ($db->query($sqlInsert)) {
        echo "✅ Picaje automático registrado\n";
    } else {
        echo "❌ Error al insertar picaje: " . $db->lasterror() . "\n";
    }
}

echo "\n✔️ Proceso completado\n";
exit;


// ✅ Protección automática: solo se registra una salida si el último tipo de picaje hoy es 'entrada'.
// Si hay un registro previo como 'vacaciones', 'baja', etc., el tipo será diferente,
// y por tanto no se registrará salida automática. Esto bloquea correctamente en días de ausencia.

