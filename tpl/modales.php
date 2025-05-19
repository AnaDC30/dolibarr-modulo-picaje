
<!-- MODAL EDITAR PICAJE/GET_PICAJE -->
<div id="modalEditar" class="modal-overlay" style="display: none;">
    <div class="modal-content" id="modalEditarContenido"></div>
</div>

<!-- MODAL CREAR PICAJE/PICAJE_INCIDENCIA -->
<div id="modalCrearPicaje" class="modal-overlay" style="display: none;">
    <div class="modal-content" id="modalCrearPicajeContenido"></div>
</div>

<!-- MODAL NUEVA INCIDENCIA -->
<div id="modal-nueva-incidencia" class="modal-overlay" style="display: none;">
  <div class="modal-content">
    <div class="modal-inner-form">
      <button class="ui-button ui-widget ui-state-default ui-corner-all cerrarModal" onclick="cerrarModalNuevaIncidencia()">×</button>
      <h2 class="titre">📩 Reportar nueva incidencia</h2>

      <form id="form-nueva-incidencia">
        <div class="formelement">
          <label for="tipo">Tipo de incidencia:</label><br>
          <select name="tipo" id="tipo" class="flat ui-widget ui-corner-all" required>
            <option value="salida_anticipada">Salida anticipada</option>
            <option value="olvido_picaje">Olvido de picaje</option>
            <option value="otro">Otro</option>
          </select>
        </div>

        <div class="formelement">
          <label for="comentario">Comentario:</label><br>
          <textarea name="comentario" id="comentario" rows="4" class="flat ui-widget ui-corner-all" required style="width: 100%;"></textarea>
        </div>

        <div class="modal-actions" style="margin-top: 1rem; text-align: center;">
          <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all">Enviar incidencia</button>
          <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all" onclick="cerrarModalNuevaIncidencia()">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- =================== -->
<!--  MODAL INCIDENCIA   -->
<!-- =================== -->
<div id="modal-status" class="modal-overlay" style="display: none;">
  <div class="modal-content">
    <div class="modal-inner-form">
      <button class="ui-button ui-widget ui-state-default ui-corner-all cerrarModal" onclick="cerrarModal()">×</button>
      <h2 class="titre">⚙️ Cambiar estado de la incidencia</h2>

      <form id="form-status">
        <input type="hidden" name="incidencia_id" id="incidencia_id">

        <div class="formelement">
          <label for="nuevo_status">Nuevo estado:</label><br>
          <select name="nuevo_status" id="nuevo_status" class="flat ui-widget ui-corner-all">
            <option value="Pendiente">Pendiente</option>
            <option value="Resuelta">Resuelta</option>
          </select>
        </div>

        <div class="formelement">
          <label for="resolucion">Mensaje de resolución:</label><br>
          <textarea id="resolucion" name="resolucion" rows="4" class="flat ui-widget ui-corner-all" placeholder="Describe brevemente la resolución..." style="width: 100%;"></textarea>
        </div>

        <div class="modal-actions" style="margin-top: 1rem; text-align: center;">
          <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all">💾 Guardar</button>
          <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all" onclick="cerrarModal()">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ======================================== -->
<!--   MODAL JUSTIFICACIÓN  ENTRADA Y SALIDA  -->
<!-- ======================================== -->

<div id="modalJustificacion" class="modal-overlay" style="display: none;">
  <div class="modal-content">
    <div class="modal-inner-form">
      <button class="ui-button ui-widget ui-state-default ui-corner-all cerrarModal" onclick="cerrarModalJustificacion()">✖</button>
      <h2 class="titre">✏️ Justificación de Picaje anticipado</h2>
      <p>Tu hora de entrada/salida prevista aún no ha llegado. Indica el motivo por el cual deseas registrar el picaje:</p>

      <form onsubmit="event.preventDefault(); enviarJustificacion();">
        <input type="hidden" name="tipo" value="">

        <label>Tipo de incidencia:</label>
        <div class="toggle-group" style="margin-bottom: 10px;">
          <input type="radio" id="opcion_extra" name="tipoIncidencia" value="horas_extra" required hidden>
          <label for="opcion_extra" class="toggle-btn">Horas extra</label>

          <input type="radio" id="opcion_entrada_anticipada" name="tipoIncidencia" value="entrada_anticipada" required hidden>
          <label for="opcion_entrada_anticipada" class="toggle-btn">Entrada anticipada</label>

          <input type="radio" id="opcion_anticipada" name="tipoIncidencia" value="salida_anticipada" required hidden>
          <label for="opcion_anticipada" class="toggle-btn">Salida anticipada</label>

          <input type="radio" id="opcion_otro" name="tipoIncidencia" value="otro" required hidden>
          <label for="opcion_otro" class="toggle-btn">Otro</label>
        </div>

        <div class="formelement">
          <label for="textoJustificacion">Motivo:</label><br>
          <textarea id="textoJustificacion" name="textoJustificacion" rows="4" required class="flat ui-widget ui-corner-all" style="width: 100%;" placeholder="Escribe aquí tu motivo..."></textarea>
        </div>

        <div class="modal-actions" style="margin-top: 1rem; text-align: center;">
          <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all" onclick="cerrarModalJustificacion()">Cancelar</button>
          <button type="submit" class="ui-button ui-widget ui-state-default ui-corner-all">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>
