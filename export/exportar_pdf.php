<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once $_SERVER['DOCUMENT_ROOT'] . '/dolibarr/main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT . '/custom/picaje/lib/dbController.php';
require_once DOL_DOCUMENT_ROOT . '/includes/tecnickcom/tcpdf/tcpdf.php';


global $db, $user;

// Recoger filtros desde la URL
$filtroFecha = GETPOST('fecha', 'alpha');
$filtroUsuario = GETPOST('usuario', 'alpha');
$filtroUserId = GETPOST('user_id', 'int');
$desdeIncidencias = GETPOST('desde', 'alpha');

// Obtener los datos con filtros si existen
$historial = obtenerHistorialPicajes($filtroFecha, $filtroUsuario, $filtroUserId, $desdeIncidencias);

// Crear PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Dolibarr - Módulo Picaje");
$pdf->SetTitle("Historial de Picaje");
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

// Título
$pdf->SetFont('', 'B', 14);
$pdf->Cell(0, 10, '📄 Historial de Picaje', 0, 1, 'C');
$pdf->Ln(4);

// Tabla
$pdf->SetFont('', '', 9);
$html = '<table border="1" cellpadding="4">
    <tr style="background-color:#eee;">
        <th><b>ID</b></th>
        <th><b>Fecha</b></th>
        <th><b>Hora</b></th>
        <th><b>Tipo</b></th>
        <th><b>Origen</b></th>';

if ($user->admin) {
    $html .= '<th><b>Usuario</b></th>';
}
$html .= '</tr>';

foreach ($historial as $row) {
    $html .= '<tr>';
    $html .= '<td>' . dol_escape_htmltag($row['id']) . '</td>';
    $html .= '<td>' . dol_escape_htmltag($row['fecha']) . '</td>';
    $html .= '<td>' . dol_escape_htmltag($row['hora']) . '</td>';
    $html .= '<td>' . dol_escape_htmltag($row['tipo']) . '</td>';
    $html .= '<td>' . dol_escape_htmltag($row['tipo_registro']) . '</td>';
    if ($user->admin) {
        $html .= '<td>' . dol_escape_htmltag($row['usuario']) . '</td>';
    }
    $html .= '</tr>';
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Debug previo (opcional)
file_put_contents('/tmp/debug_pdf_output.txt', ob_get_contents());

// Limpiar todos los buffers activos
while (ob_get_level()) {
    ob_end_clean();
}
$pdf->Output('historial_picaje_' . date('Ymd_His') . '.pdf', 'D');

exit;
