// =========================
// MODAL DE EDICIÓN DE PICAJE
// =========================

function abrirModalEditar(id) {
    document.getElementById("modalEditar").style.display = "flex";

    fetch(`${URL_GET_PICAJE}?id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById("modalEditarContenido").innerHTML = html;
        })
        .catch(error => {
            console.error("Error al cargar el formulario:", error);
            document.getElementById("modalEditarContenido").innerHTML = "<p>Error al cargar el formulario.</p>";
        });
}

function cerrarModalEditar() {
    document.getElementById('modalEditar').style.display = 'none';
    document.getElementById('modalEditarContenido').innerHTML = '';
}


// =========================
// MENSAJE DE ÉXITO
// =========================

function mostrarMensajeExito(mensaje) {
    const contenedor = document.getElementById("modalEditarContenido");
    contenedor.innerHTML = "";

    const alerta = document.createElement("div");
    alerta.className = "mensaje-exito";
    alerta.textContent = mensaje;

    const botonHistorial = document.createElement("button");
    botonHistorial.className = "btn-historial";
    botonHistorial.textContent = "📄 Ver historial de modificaciones";
    botonHistorial.onclick = () => {
        window.open(URL_LOG_MODIFICACIONES, '_blank');
    };

    contenedor.appendChild(alerta);
    contenedor.appendChild(botonHistorial);

    setTimeout(() => {
        cerrarModalEditar();
        location.reload();
    }, 4000);
}

// =======================
// GUARDAR EDICIÓN (AJAX)
// =======================

function guardarEdicion(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    formData.append('token', DOLIBARR_CSRF_TOKEN);

    fetch(URL_MODIFICAR_PICAJE, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Respuesta no válida del servidor: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarMensajeExito("✔ Cambios guardados correctamente");
        } else {
            alert("❌ Error al guardar: " + (data.error || "Revisa los datos"));
        }
    })
    .catch(error => {
        console.error("Error al guardar:", error);
        alert("❌ Error inesperado al enviar el formulario.");
    });

    return false;
}


// ==========
// UBICACIÓN
// ==========

function inicializarPicaje(haEntrada, haSalida) {
    const form = document.getElementById('form-picaje');
    const latInput = document.getElementById("latitud");
    const lonInput = document.getElementById("longitud");
    const tipoInput = document.getElementById("tipo_picaje");

    const modalJustificacion = document.getElementById('modalJustificacion');
    let ubicacionObtenida = false;

    // Obtener ubicación del navegador
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            latInput.value = position.coords.latitude;
            lonInput.value = position.coords.longitude;
            ubicacionObtenida = true;
        }, function () {
            alert("❌ No se pudo obtener la ubicación. No podrás picar sin permitir la localización.");
        });
    } else {
        alert("⚠️ Este navegador no soporta geolocalización.");
    }

    // Evento al enviar formulario
    form.addEventListener('submit', function (e) {
        if (!latInput.value || !lonInput.value || !ubicacionObtenida) {
            e.preventDefault();
            alert("❌ No se ha detectado la ubicación. No se puede registrar el picaje.");
            return;
        }

        if (!haEntrada) {
            tipoInput.value = 'entrada';
        } else if (haEntrada && !haSalida) {
            e.preventDefault(); // Detenemos el envío hasta validar si es salida anticipada

            fetch('../ajax/validar_salida.php')
                .then(response => response.json())
                .then(data => {
                    if (data.salida_anticipada) {
                        modalJustificacion.style.display = 'flex';
                    } else {
                        tipoInput.value = 'salida';
                        form.submit();
                    }
                })
                .catch(err => {
                    alert("❌ Error al validar hora de salida.");
                    console.error(err);
                });
        }
    });
}


// Función para cerrar el modal de justificación
function cerrarModalJustificacion() {
    const modal = document.getElementById('modalJustificacion');
    modal.style.display = 'none';
}

// Función para confirmar y enviar justificación
function enviarJustificacion() {
    const justificacion = document.getElementById('textoJustificacion').value.trim();
    const inputHidden = document.getElementById('inputJustificacion');
    const tipoInput = document.getElementById("tipo_picaje");

    if (!justificacion) {
        alert("⚠️ Debes indicar un motivo.");
        return;
    }

    inputHidden.value = justificacion;
    tipoInput.value = 'salida';

    document.getElementById('form-picaje').submit();
}


function verUbicacion(id) {
    document.getElementById("modalUbicacion").style.display = "flex";

    fetch(`${URL_GET_UBICACION}?id=${id}`)
        .then(response => response.json())
        .then(data => {
            console.log("📦 Datos de ubicación recibidos:", data); // 🔍 Aquí lo añadimos

            const contenedor = document.getElementById("modalUbicacionContenido");
            if (data.success) {
                contenedor.innerHTML = `
                    <h3>📍 Ubicación del registro</h3>
                    <p><strong>Usuario:</strong> ${data.usuario}</p>
                    <p><strong>Fecha:</strong> ${data.fecha}</p>
                    <p><strong>Hora:</strong> ${data.hora}</p>
                    <p><strong>Latitud:</strong> ${data.latitud}</p>
                    <p><strong>Longitud:</strong> ${data.longitud}</p>
                    <a href="https://www.google.com/maps?q=${data.latitud},${data.longitud}" target="_blank">🌍 Ver en Google Maps</a>
                `;
            } else {
                contenedor.innerHTML = `<p>Error: ${data.error}</p>`;
            }
        })
        .catch(error => {
            console.error("Error al obtener ubicación:", error);
            document.getElementById("modalUbicacionContenido").innerHTML = "<p>❌ Error al cargar los datos de ubicación.</p>";
        });
}




// =================
//  CERRAR MODALES
// =================

window.addEventListener('click', function (event) {
    const modalEditar = document.getElementById('modalEditar');
    const modalUbicacion = document.getElementById('modalUbicacion');

    if (modalEditar && event.target === modalEditar) {
        cerrarModalEditar();
    }

    if (modalUbicacion && event.target === modalUbicacion) {
        cerrarModalUbicacion();
    }
});

function cerrarModalEditar() {
    const modal = document.getElementById('modalEditar');
    const contenido = document.getElementById('modalEditarContenido');

    if (modal && contenido) {
        modal.style.display = 'none';
        contenido.innerHTML = '';
    }
}

function cerrarModalUbicacion() {
    const modal = document.getElementById('modalUbicacion');
    const contenido = document.getElementById('modalUbicacionContenido');

    if (modal && contenido) {
        modal.style.display = 'none';
        contenido.innerHTML = '';
    }
}


// =====================
// JUSTIFICACIÓN PICAJE
// =====================

function enviarJustificacion() {
    const texto = document.getElementById('textoJustificacion').value.trim();
    if (!texto) {
        alert("⚠️ Debes escribir una justificación.");
        return;
    }

    document.getElementById('inputJustificacion').value = texto;
    document.getElementById('tipo_picaje').value = 'salida';
    document.getElementById('form-picaje').submit();
}

function cerrarModalJustificacion() {
    document.getElementById('modalJustificacion').style.display = 'none';
}


// ==========================
// PICAJE AJAX (entrada/salida)
// ==========================

function inicializarPicaje(haEntrada, haSalida) {
    const tipoInput = document.getElementById('tipo_picaje');

    if (!tipoInput) return;

    if (!haEntrada && !haSalida) {
        tipoInput.value = 'entrada';
    } else if (haEntrada && !haSalida) {
        tipoInput.value = 'salida';
    } else {
        tipoInput.value = ''; 
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const formPicaje = document.getElementById('form-picaje');
    const botonPicar = document.getElementById('boton-picar');

    if (formPicaje && botonPicar) {
        formPicaje.addEventListener('submit', function (e) {
            e.preventDefault();

            botonPicar.disabled = true;
            botonPicar.textContent = '⏳ Obteniendo ubicación...';

            if (!navigator.geolocation) {
                alert("⚠️ Tu navegador no soporta geolocalización.");
                botonPicar.disabled = false;
                botonPicar.textContent = '📍 Picar';
                return;
            }

            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                document.getElementById('latitud').value = lat;
                document.getElementById('longitud').value = lng;

                // Confirmación visual
                console.log("✅ Latitud recogida:", lat);
                console.log("✅ Longitud recogida:", lng);

                const formData = new FormData(formPicaje);
                const tokenInput = document.querySelector('input[name="token"]');
                if (tokenInput) {
                    formData.append('token', tokenInput.value);
                }

                const actionURL = formPicaje.getAttribute('action');
                botonPicar.textContent = '⏳ Registrando...';

                fetch(actionURL, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.text())
                .then(text => {
                    console.log("🔍 Respuesta cruda del servidor:", text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            alert(data.message || "✔ Picaje registrado correctamente");
                            location.reload();
                        } else {
                            alert("❌ " + (data.error || "Error al registrar el picaje."));
                            botonPicar.disabled = false;
                            botonPicar.textContent = '📍 Picar';
                        }
                    } catch (e) {
                        console.error("❌ No es JSON válido:", text);
                        alert("❌ Error inesperado del servidor. Revisa la consola.");
                        botonPicar.disabled = false;
                        botonPicar.textContent = '📍 Picar';
                    }
                })
                .catch(error => {
                    console.error("Error en el picaje:", error);
                    alert("❌ Error inesperado.");
                    botonPicar.disabled = false;
                    botonPicar.textContent = '📍 Picar';
                });

            }, function (error) {
                alert("⚠️ No se pudo obtener la ubicación. Verifica los permisos del navegador.");
                botonPicar.disabled = false;
                botonPicar.textContent = '📍 Picar';
            });
        });
    }
});




