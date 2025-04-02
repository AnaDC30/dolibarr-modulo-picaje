<?php

file_put_contents("C:/xampp/htdocs/dolibarr/custom/picaje/log_prueba.txt", "🎯 Entró a actions_picaje.class.php\n", FILE_APPEND);


class ActionsPicaje
{
    public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        global $db, $langs;

        // Hook en ficha de usuario
        if ($parameters['currentcontext'] == 'usercard' && $object->id > 0) {
            require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';

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

            print ' - <a href="' . dol_buildpath('/custom/picaje/tpl/editar_horario.php?user_id=' . $object->id, 1) . '">Editar</a>';
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

            print ' - <a href="' . dol_buildpath('/custom/picaje/tpl/editar_horario_grupo.php?grupo_id=' . $object->id, 1) . '">Editar</a>';
            print '</td></tr>';
        }

        return 0;
    }

    public function top_right_menu($parameters, &$menus){

        error_log("✅ Entró a top_right_menu PICAJE", 3, "C:/xampp/php/logs/ph_error_log.txt"); // <- AJUSTA ESTA RUTA SI LA TUYA ES OTRA
        file_put_contents("C:/xampp/htdocs/dolibarr/custom/picaje/log_prueba.txt", "🚨 Entró a top_right_menu()\n", FILE_APPEND);



        
        global $user, $langs, $conf;

        // Mostrar solo si está activado en configuración
        if (empty($conf->global->PICAR_MOSTRAR_BOTON_HEADER)) return 0;

        // Ejecutar salida automática si aplica
        require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/auto_salida.php';
        ejecutarSalidaAutomaticaUsuario($user->id);

        if ($user->id > 0) {
            require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';
            $estado = getEstadoPicajeUsuario($user->id);

            $idBoton = "btnPicajeHeader";
            $texto = '';
            $icono = '';
            $claseEstado = '';

            // Determinar estado actual
            if (!$estado['entrada']) {
                $texto = "Picar entrada";
                $icono = "⏱️";
                $claseEstado = "picar-entrada";
            } elseif (!$estado['salida']) {
                $texto = "Picar salida";
                $icono = "📤";
                $claseEstado = "picar-salida";
            } else {
                $texto = "Jornada completada";
                $icono = "✅";
                $claseEstado = "picar-completado";
            }

            // HTML del botón encapsulado para evitar conflictos visuales
            $iconClass = 'fa-clock-o';
            if (!$estado['entrada']) $iconClass = 'fa-sign-in';
            elseif (!$estado['salida']) $iconClass = 'fa-sign-out';
            else $iconClass = 'fa-check';
            
            $html = '<div class="classfortooltip inline-block login_block_elem inline-block" style="padding-right: 2px;" title="' . dol_escape_htmltag($texto) . '">';
            $html .= '<a id="' . $idBoton . '" class="' . $claseEstado . '" href="#">';
            $html .= '<span class="fa ' . $iconClass . ' atoplogin valignmiddle"></span>';
            $html .= '</a>';
            $html .= '</div>';
            
            $menus[] = array(
                'url' => '#',
                'titre' => $texto,
                'level' => 0,
                'html' => $html
            );
            

            // Script JavaScript para gestionar el clic y enviar ubicación
            print '<script>
            document.addEventListener("DOMContentLoaded", function () {
                const btn = document.getElementById("' . $idBoton . '");
                if (!btn || btn.classList.contains("picar-completado")) return;

                btn.addEventListener("click", function (e) {
                    e.preventDefault();

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (pos) {
                            const lat = pos.coords.latitude;
                            const lon = pos.coords.longitude;

                            btn.innerText = "⏳ Registrando...";
                            btn.style.opacity = 0.6;

                            fetch("' . dol_buildpath('/custom/picaje/core/modules/picar_desde_header.php', 1) . '?latitud=" + lat + "&longitud=" + lon)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        alert(data.mensaje);
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

        if (empty($conf->global->PICAR_AUTO_LOGIN)) return 0;
        if (empty($user->id)) return 0;

        $fecha = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total FROM llx_picaje 
                WHERE usuario_id = " . (int) $user->id . " 
                AND fecha = '" . $db->escape($fecha) . "' 
                AND tipo = 'entrada'";

        $res = $db->query($sql);
        if ($res && $db->num_rows($res)) {
            $row = $db->fetch_object($res);
            if ((int) $row->total > 0) return 0;
        }

        // Registrar entrada automática
        $hora = date('H:i:s');
        $sql_insert = "INSERT INTO llx_picaje (fecha, hora, tipo, usuario_id, latitud, longitud, tipo_registro)
                   VALUES (
                       '" . $db->escape($fecha) . "',
                       '" . $db->escape($hora) . "',
                       'entrada',
                       " . (int) $user->id . ",
                       NULL, NULL,
                       'auto_login'
                   )";

        $db->query($sql_insert);
        $_SESSION['entrada_auto_login'] = 1;

        return 0;
    }

    public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {
        // Mensajes tras auto login o salida automática
        if (!empty($_SESSION['entrada_auto_login'])) {
            setEventMessages("📍 Entrada registrada automáticamente al iniciar sesión.", null, 'mesgs');
            unset($_SESSION['entrada_auto_login']);
        }

        if (!empty($_SESSION['salida_auto_salida'])) {
            setEventMessages("📤 Salida registrada automáticamente por horario.", null, 'mesgs');
            unset($_SESSION['salida_auto_salida']);
        }

        return 0;
    }
}
