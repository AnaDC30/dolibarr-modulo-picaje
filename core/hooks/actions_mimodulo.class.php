<?php

class ActionsMimodulo
{
    public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        global $db, $langs;

        // Hook en ficha de usuario
        if ($parameters['currentcontext'] == 'usercard' && $object->id > 0) {
            require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';

            print '<tr><td><strong>⏱️ Horario de picaje:</strong></td><td>';

            $horario = getHorarioUsuario($object->id);

            if ($horario) {
                print 'Salida prevista: <strong>' . substr($horario->hora_salida, 0, 5) . '</strong>';
                if (!empty($horario->salida_automatica)) print ' (automática)';

                // Mostrar aviso si se hereda de un grupo
                if (!empty($horario->heredado_de_grupo)) {
                    print '<div class="warning" style="margin-top:8px;color:#b36b00;">⚠️ Este horario se hereda del grupo <strong>' . $horario->heredado_de_grupo . '</strong>. Verifique si es válido para este usuario.</div>';
                }
            } else {
                print '<em>No tiene horario asignado</em>';
            }

            print ' - <a href="' . dol_buildpath('/custom/mimodulo/tpl/editar_horario.php?user_id=' . $object->id, 1) . '">Editar</a>';
            print '</td></tr>';
        }

        // Hook en ficha de grupo (departamento)
        if ($parameters['currentcontext'] == 'groupcard' && $object->id > 0) {
            $sql = "SELECT hora_salida, salida_automatica FROM llx_picaje_horarios 
                    WHERE fk_departement = " . (int) $object->id . " 
                    AND entity = " . (int) $GLOBALS['conf']->entity . " 
                    LIMIT 1";

            $res = $db->query($sql);

            print '<tr><td><strong>⏱️ Horario de grupo:</strong></td><td>';

            if ($res && $db->num_rows($res)) {
                $row = $db->fetch_object($res);
                print 'Salida prevista: <strong>' . substr($row->hora_salida, 0, 5) . '</strong>';
                if ($row->salida_automatica) print ' (automática)';
            } else {
                print '<em>No tiene horario asignado</em>';
            }

            print ' - <a href="' . dol_buildpath('/custom/mimodulo/tpl/editar_horario_grupo.php?grupo_id=' . $object->id, 1) . '">Editar</a>';
            print '</td></tr>';
        }

        return 0;
    }

public function top_right_menu($parameters, &$menus){
    global $user, $langs;

    if ($user->id > 0) {
        require_once DOL_DOCUMENT_ROOT . '/custom/mimodulo/core/modules/dbController.php';
        $estado = getEstadoPicajeUsuario($user->id);

        $idBoton = "btnPicajeHeader";
        $texto = '';
        $color = '';
        $icono = '';

        if (!$estado['entrada']) {
            $texto = "Picar entrada";
            $color = "#2ecc71";
            $icono = "⏱️";
        } elseif ($estado['entrada'] && !$estado['salida']) {
            $texto = "Picar salida";
            $color = "#e67e22";
            $icono = "📤";
        } else {
            $texto = "Jornada completada";
            $color = "#95a5a6";
            $icono = "✅";
        }

        // Botón visual
        $menus[] = [
            'url' => '#',
            'titre' => $texto,
            'level' => 0,
            'html' => '<a id="' . $idBoton . '" class="butAction" style="background-color:' . $color . ';color:white;margin-right:10px;">' . $icono . ' ' . $texto . '</a>'
        ];

        // Script inline para gestionar el picaje vía AJAX
        print '<script>
        document.addEventListener("DOMContentLoaded", function () {
            const btn = document.getElementById("' . $idBoton . '");
            if (!btn || btn.innerText.includes("Jornada completada")) return;

            btn.addEventListener("click", function (e) {
                e.preventDefault();

                // Obtener ubicación
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        const lat = pos.coords.latitude;
                        const lon = pos.coords.longitude;

                        btn.innerText = "⏳ Registrando...";
                        btn.style.opacity = 0.6;

                        fetch("' . dol_buildpath('/custom/mimodulo/core/modules/picar_desde_header.php', 1) . '?latitud=" + lat + "&longitud=" + lon)
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert(data.mensaje);
                                    // Recargar para actualizar botón (mejor que manipular HTML)
                                    location.reload();
                                } else {
                                    alert("❌ " + (data.error || "Error desconocido"));
                                    btn.style.opacity = 1;
                                }
                            })
                            .catch(err => {
                                alert("❌ Error al enviar picaje.");
                                console.error(err);
                                btn.style.opacity = 1;
                            });

                    }, function (err) {
                        alert("❌ No se pudo obtener la ubicación.");
                    });
                } else {
                    alert("❌ Tu navegador no permite geolocalización.");
                }
            });
        });
        </script>';
    }

    return 0;
}

public function afterLogin($parameters, &$object, &$action, $hookmanager)
{
    global $conf, $db, $user;

    // Si no está activado en la config → salir
    if (empty($conf->global->PICAR_AUTO_LOGIN)) return 0;

    // Solo usuarios válidos
    if (empty($user->id)) return 0;

    // Verificar si ya tiene entrada hoy
    $fecha = date('Y-m-d');
    $sql = "SELECT COUNT(*) as total FROM llx_picaje 
            WHERE usuario_id = " . (int) $user->id . " 
            AND fecha = '" . $db->escape($fecha) . "' 
            AND tipo = 'entrada'";

    $res = $db->query($sql);
    if ($res && $db->num_rows($res)) {
        $row = $db->fetch_object($res);
        if ((int) $row->total > 0) return 0; // Ya tiene entrada hoy
    }

    // Registrar entrada automática
    $hora = date('H:i:s');
    $sql_insert = "INSERT INTO llx_picaje (fecha, hora, tipo, usuario_id, latitud, longitud)
                   VALUES (
                       '" . $db->escape($fecha) . "',
                       '" . $db->escape($hora) . "',
                       'entrada',
                       " . (int) $user->id . ",
                       NULL, NULL
                   )";

    $db->query($sql_insert);
    $_SESSION['entrada_auto_login'] = 1;
    return 0;
}

//Mnesaje de entrada/salida registrada

public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
{
    if (!empty($_SESSION['entrada_auto_login'])) {
        setEventMessages("📍 Entrada registrada automáticamente al iniciar sesión.", null, 'mesgs');
        unset($_SESSION['entrada_auto_login']); // evitar que se muestre de nuevo
    }

    return 0;
}


}

