

// =========================
// MODAL DE EDICIÓN DE PICAJE
// =========================

function abrirModalEditar(id) {
    document.getElementById("modalEditar").style.display = "flex";

    fetch(dol_buildpath('/custom/picaje/core/modules/get_picaje.php?id=' + id, 1)) 
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

// Cerrar modal al hacer clic fuera del contenido
window.addEventListener('click', function (event) {
    const modal = document.getElementById('modalEditar');
    if (event.target === modal) {
        cerrarModalEditar();
    }
});

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
        window.open(dol_buildpath('/custom/picaje/tpl/log_modificaciones.php', 1), '_blank');
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

    fetch(dol_buildpath('/custom/picaje/core/modules/modificar_picaje.php', 1), {
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

function verUbicacion(id) {
    document.getElementById("modalUbicacion").style.display = "flex";

    fetch(dol_buildpath(`/custom/picaje/core/modules/get_ubicacion.php?id=${id}`, 1))
        .then(response => response.json())
        .then(data => {
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

function cerrarModalUbicacion() {
    document.getElementById("modalUbicacion").style.display = "none";
    document.getElementById("modalUbicacionContenido").innerHTML = '';
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
