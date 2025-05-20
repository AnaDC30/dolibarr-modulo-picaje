<?php
require_once DOL_DOCUMENT_ROOT . '/core/lib/pdf.lib.php';

/**
 * Clase para generar un PDF de resumen mensual de picaje con diseño visual mejorado.
 */
class ResumenMensualPDF
{
    /**
     * Usuario al que corresponde el resumen
     * @var User
     */
    protected $userObj;

    /**
     * Fecha de inicio del periodo
     * @var string (Y-m-d)
     */
    protected $start;

    /**
     * Fecha de fin del periodo
     * @var string (Y-m-d)
     */
    protected $end;

    /**
     * Registros de picaje (texto formateado)
     * @var array
     */
    protected $registros;

    /**
     * Total de horas trabajadas
     * @var float
     */
    protected $totalHoras;

    /**
     * Horas extra calculadas
     * @var float
     */
    protected $horasExtras;

    /**
     * Número de salidas anticipadas
     * @var int
     */
    protected $salidasAnticipadas;

    /**
     * Constructor.
     *
     * @param User   $userObj             Usuario Dolibarr
     * @param string $start               Fecha inicio (Y-m-d)
     * @param string $end                 Fecha fin (Y-m-d)
     * @param array  $registros           Array de líneas "YYYY-MM-DD: Entrada - HH:MM, Salida - HH:MM"
     * @param float  $totalHoras          Total de horas trabajadas
     * @param float  $horasExtras         Horas extra
     * @param int    $salidasAnticipadas  Número de salidas anticipadas
     */
    public function __construct(
        $userObj,
        string $start,
        string $end,
        array $registros,
        float $totalHoras,
        float $horasExtras,
        int $salidasAnticipadas
    ) {
        $this->userObj = $userObj;
        $this->start = $start;
        $this->end = $end;
        $this->registros = $registros;
        $this->totalHoras = $totalHoras;
        $this->horasExtras = $horasExtras;
        $this->salidasAnticipadas = $salidasAnticipadas;
    }

    /**
     * Genera el PDF y lo guarda en la ruta especificada.
     *
     * @param string $outputPath Ruta completa donde guardar el PDF
     * @return bool True si se guardó correctamente, false en caso contrario
     */
    public function generate(string $outputPath): bool
    {
        global $langs;

        // Instancia de TCPDF (o librería Dolibarr)
        $pdf = pdf_getInstance();
        $pdf->SetTitle("Resumen mensual de picajes");
        $pdf->SetAuthor("Dolibarr");
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Márgenes
        $pdf->SetMargins(15, 20, 15);

        // Título
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetFont('', 'B', 16);
        $pdf->MultiCell(0, 10, "Resumen Horario Mensual", 0, 'C', true);
        $pdf->Ln(4);

        // Datos del usuario y periodo
        $pdf->SetFont('', '', 10);
        $nombre = $this->userObj->getFullName($langs);
        $pdf->MultiCell(0, 6, "Usuario: $nombre", 0);
        $pdf->MultiCell(0, 6, "Periodo: {$this->start} a {$this->end}", 0);
        $pdf->Ln(6);

        // Listado de registros
        $pdf->SetFont('', '', 10);
        foreach ($this->registros as $line) {
            $pdf->MultiCell(0, 6, $line, 0, 'L');
        }

        // Totales
        $pdf->Ln(6);
        $pdf->SetFont('', 'B', 11);
        $pdf->MultiCell(0, 6, "Total horas trabajadas: " . round($this->totalHoras, 2));
        $pdf->MultiCell(0, 6, "Horas extra: " . round($this->horasExtras, 2));
        $pdf->MultiCell(0, 6, "Salidas anticipadas: " . $this->salidasAnticipadas);

        // Firma
        $pdf->Ln(12);
        $pdf->MultiCell(0, 6, "Firma del trabajador:", 0);
        $pdf->Ln(12);
        $pdf->Cell(80, 0, '', 'B');

        // Guardar PDF en disco
        return (bool) $pdf->Output($outputPath, 'F');
    }
}

