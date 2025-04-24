<?php
require_once DOL_DOCUMENT_ROOT . '/core/lib/pdf.lib.php';

/**
 * Genera un PDF de resumen mensual de picaje con diseño visual mejorado.
 *
 * @param DoliDB $db
 * @param User $userObj
 * @param array $dias Array con las fechas y horas de entrada/salida.
 * @param float $totalHoras
 * @param float $horasExtras
 * @param int $salidasAnticipadas
 * @param string $start Fecha de inicio del resumen.
 * @param string $end Fecha de fin del resumen.
 * @param string $outputPath Ruta completa donde guardar el PDF.
 * @return bool
 */
function generarResumenMensualPDF($db, $userObj, $dias, $totalHoras, $horasExtras, $salidasAnticipadas, $start, $end, $outputPath)
{
    global $langs;

    $pdf = pdf_getInstance();
    $pdf->SetTitle("Resumen mensual de picajes");
    $pdf->SetAuthor("Dolibarr");
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();

    // Márgenes
    $pdf->SetMargins(15, 20, 15);

    // Cabecera
    $pdf->SetFillColor(230, 230, 230);
    $pdf->SetFont('', 'B', 16);
    $pdf->MultiCell(0, 10, "Resumen Horario Mensual", 0, 'C', true);
    $pdf->Ln(4);

    // Datos del usuario
    $pdf->SetFont('', '', 10);
    $nombre = $userObj->getFullName($langs);
    $pdf->MultiCell(0, 6, "Usuario: $nombre", 0);
    $pdf->MultiCell(0, 6, "Periodo: $start a $end", 0);
    $pdf->Ln(4);

    // Tabla de registros
    $pdf->SetFont('', 'B', 11);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(40, 7, "Fecha", 1, 0, 'C', true);
    $pdf->Cell(40, 7, "Hora Entrada", 1, 0, 'C', true);
    $pdf->Cell(40, 7, "Hora Salida", 1, 0, 'C', true);
    $pdf->Cell(60, 7, "Observaciones", 1, 1, 'C', true);

    $pdf->SetFont('', '', 10);
    foreach ($dias as $fecha => $data) {
        $entrada = $data['entrada'] ?? '---';
        $salida = $data['salida'] ?? '---';
        $obs = ' ';
        if (!isset($data['salida'])) {
            $obs = "Sin salida registrada";
        } elseif (!isset($data['entrada'])) {
            $obs = "Sin entrada registrada";
        }

        $pdf->Cell(40, 6, $fecha, 1);
        $pdf->Cell(40, 6, $entrada, 1);
        $pdf->Cell(40, 6, $salida, 1);
        $pdf->Cell(60, 6, $obs, 1);
        $pdf->Ln();
    }

    $pdf->Ln(6);
    $pdf->SetFont('', 'B', 11);
    $pdf->MultiCell(0, 6, "Total horas trabajadas: " . round($totalHoras, 2));
    $pdf->MultiCell(0, 6, "Horas extra: " . round($horasExtras, 2));
    $pdf->MultiCell(0, 6, "Salidas anticipadas: $salidasAnticipadas");

    // Espacio para firma
    $pdf->Ln(12);
    $pdf->MultiCell(0, 6, "Firma del trabajador:", 0);
    $pdf->Ln(12);
    $pdf->Cell(80, 0, '', 'B'); // Línea para firma

    // Guardar PDF
    return $pdf->Output($outputPath, 'F');
}
