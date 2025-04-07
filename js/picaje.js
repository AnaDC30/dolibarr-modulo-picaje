
// ==========================
// MODAL DE EDICIÓN DE PICAJE
// ==========================

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


// ==================
// MENSAJE DE ÉXITO
// ==================

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


// ==============================
// CONTROL DE PICAJE Y UBICACIÓN
// ==============================

function inicializarPicaje(haEntrada, haSalida, salidaManualJustificada) {
    const form = document.getElementById('form-picaje');
    const latInput = document.getElementById("latitud");
    const lonInput = document.getElementById("longitud");
    const tipoInput = document.querySelector('input[name="tipo"]');
    const modalJustificacion = document.getElementById('modalJustificacion');
    const boton = document.getElementById('boton-picar');

    let ubicacionObtenida = false;

    // =======================================
    // 1. Ajustar texto del botón según estado
    // =======================================
    if (!boton) {
        console.warn("⚠️ No se encontró el botón de picaje en el DOM.");
        return;
    }

    if (!haEntrada) {
        boton.textContent = "📍 Picar entrada";
    } else if (haEntrada && !haSalida) {
        boton.textContent = "📍 Picar salida";
    } else if (haEntrada && haSalida) {
        boton.textContent = "✅ Picaje completado";
        boton.disabled = true;
        boton.classList.add('disabled');
    }

    // ==================================
    // 2. Obtener ubicación del navegador
    // ==================================
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

    // ==================================
    // 3. Evento de envío del formulario
    // ==================================
    form.addEventListener('submit', function (e) {
        // Verificar ubicación
        if (!latInput.value || !lonInput.value || !ubicacionObtenida) {
            e.preventDefault();
            alert("❌ No se ha detectado la ubicación. No se puede registrar el picaje.");
            return;
        }

        const tipo = tipoInput.value;

        // Caso: salida manual con justificación
        if (tipo === 'salida' && salidaManualJustificada) {
            e.preventDefault();
            modalJustificacion.style.display = 'flex';
            return;
        }

        // Caso: lógica antigua (opcional)
        if (!haEntrada) {
            tipoInput.value = 'entrada';
        } else if (haEntrada && !haSalida) {
            // Si no hay salida manual justificada activa, validar si es salida anticipada
            if (!salidaManualJustificada) {
                e.preventDefault();
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
        }
    });
}

// =======================================
//   MODAL DE JUSTIFICACION/INCIDENCIAS
// =======================================
function enviarJustificacion() {
    const tipo = document.querySelector('input[name="tipoIncidencia"]:checked');
    const motivo = document.getElementById('textoJustificacion').value.trim();
  
    if (!tipo || !motivo) {
      alert("Debes seleccionar el tipo de incidencia y escribir una justificación.");
      return;
    }
  
    fetch('/dolibarr/custom/picaje/ajax/registrar_incidencia.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        token: csrfToken, // 
        tipo: tipo.value,
        justificacion: motivo
      })
    })
    .then(async res => {
      try {
        const data = await res.json();
  
        if (data.success) {
          mostrarToast("✅ Justificación registrada correctamente."); 
          cerrarModalJustificacion();
        } else {
          alert("❌ Error: " + (data.error || "No se pudo registrar la incidencia."));
        }
      } catch (e) {
        console.error("❌ Error al interpretar JSON:", e);
        alert("❌ Respuesta inesperada del servidor.");
      }
    })
    .catch(err => {
      console.error("❌ Error de red:", err);
      alert("❌ No se pudo conectar con el servidor.");
    });
}
  

function abrirModalJustificacion() {
    document.getElementById('modalJustificacion').style.display = 'flex';
  }
  


// ===============================
//   MODAL DE STATUS INCIDENCIAS
// ===============================
document.addEventListener('DOMContentLoaded', function () {
    // Mostrar modal al hacer clic en el botón
    document.querySelectorAll('.btn-status').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const status = btn.dataset.status;
  
        if (!id || !status) return;
  
        document.getElementById('incidencia_id').value = id;
        document.getElementById('nuevo_status').value = status;
  
        // Aplicar color visual al <select> según estado
        applySelectStyle(status);
  
        // Mostrar el modal centrado
        document.getElementById('modal-status').style.display = 'flex';
      });
    });
  
    // Cerrar modal desde función global
    window.cerrarModal = function () {
      document.getElementById('modal-status').style.display = 'none';
    };
  
    // Enviar formulario con fetch
    const formStatus = document.getElementById('form-status');
    if (formStatus) {
      formStatus.addEventListener('submit', function (e) {
        e.preventDefault();
  
        const id = document.getElementById('incidencia_id').value;
        const status = document.getElementById('nuevo_status').value;
  
        fetch('ajax/cambiar_status_incidencia.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `id=${id}&status=${encodeURIComponent(status)}`
        })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              document.getElementById('modal-status').style.display = 'none';
              location.reload();
            } else {
              alert('Error: ' + data.error);
            }
          })
          .catch(err => {
            console.error('Error AJAX:', err);
            alert('Error inesperado al guardar.');
          });
      });
    }
  
    // Detectar cambios manuales en el <select>
    const selectStatus = document.getElementById('nuevo_status');
    if (selectStatus) {
      selectStatus.addEventListener('change', () => {
        applySelectStyle(selectStatus.value);
      });
    }
  
    // Función para aplicar clases visuales al select
    function applySelectStyle(value) {
      const select = document.getElementById('nuevo_status');
      select.classList.remove('pendiente', 'resuelta');
  
      if (value === 'Pendiente') select.classList.add('pendiente');
      if (value === 'Resuelta') select.classList.add('resuelta');
    }
  });
  

// ==========================
// MODAL DE INCIDENCIA USER
// ==========================

const modalNueva = document.getElementById('modal-nueva-incidencia');
const formNueva = document.getElementById('form-nueva-incidencia');

window.cerrarModalNuevaIncidencia = function () {
  modalNueva.style.display = 'none';
};

if (formNueva) {
  formNueva.addEventListener('submit', function (e) {
    e.preventDefault();

    const tipo = document.getElementById('tipo').value;
    const comentario = document.getElementById('comentario').value;

    fetch('ajax/insert_incidencia_user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `tipo=${encodeURIComponent(tipo)}&comentario=${encodeURIComponent(comentario)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        modalNueva.style.display = 'none';
        location.reload();
      } else {
        alert('Error al guardar: ' + data.error);
      }
    })
    .catch(err => {
      console.error('Error en la solicitud:', err);
      alert('Error inesperado al enviar la incidencia.');
    });
  });
}


// ==========================
// VER UBICACION EN REGISTRO
// ==========================

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

function cerrarModalJustificacion() {
    document.getElementById('modalJustificacion').style.display = 'none';
}


// ================
//  MOSTRAR TOAST 
// ================

function mostrarToast(mensaje) {
    const toast = document.getElementById('toast');
    toast.textContent = mensaje;
    toast.style.display = 'block';
  
    setTimeout(() => {
      toast.style.display = 'none';
    }, 4000);
  }
  
