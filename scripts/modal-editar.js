// =========================
// MODAL DE EDICIÓN DE PICAJE
// =========================

function abrirModalEditar(id) {
    // Mostrar el modal
    document.getElementById("modalEditar").style.display = "flex";

    // Cargar el contenido del formulario por AJAX
    fetch('../core/modules/get_picaje.php?id=' + id) 
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

    // Vacía el contenido del modal
    contenedor.innerHTML = "";

    // Mensaje de éxito
    const alerta = document.createElement("div");
    alerta.className = "mensaje-exito";
    alerta.textContent = mensaje;

    // Botón para ver historial
    const botonHistorial = document.createElement("button");
    botonHistorial.className = "btn-historial";
    botonHistorial.textContent = "📄 Ver historial de modificaciones";
    botonHistorial.onclick = () => {
        window.open('log_modificaciones.php', '_blank');
    };

    // Contenedor final
    contenedor.appendChild(alerta);
    contenedor.appendChild(botonHistorial);

    // Cerrar automáticamente después de unos segundos (opcional)
    setTimeout(() => {
        cerrarModalEditar();
        location.reload();
    }, 4000);
}


// =========================
// GUARDAR EDICIÓN (AJAX)
// =========================

function guardarEdicion(event) {
    event.preventDefault(); 

    const form = event.target;
    const formData = new FormData(form);

     //Añadir token CSRF generado desde PHP
    formData.append('token', DOLIBARR_CSRF_TOKEN); // para evitar CSRF

    fetch('../core/modules/modificar_picaje.php', {
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
        console.log("Respuesta del servidor:", data);
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


