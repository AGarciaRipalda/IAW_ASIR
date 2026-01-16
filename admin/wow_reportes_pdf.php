<?php
/**
 * Generador de Reportes PDF - WoW Test Manager
 * 
 * Sistema de exportación profesional de reportes a PDF
 * usando TCPDF con diseño temático de World of Warcraft
 */

require_once __DIR__ . '/../includes/wow_auth.php';
verificarLogin();
verificarRol('admin');

// Verificar si TCPDF está instalado
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('
        <h1>Error: TCPDF no instalado</h1>
        <p>Por favor, ejecuta <code>composer install</code> en el directorio raíz del proyecto.</p>
        <p>Si no tienes Composer, descárgalo de <a href="https://getcomposer.org/">getcomposer.org</a></p>
        <a href="wow_reportes.php">Volver a Reportes</a>
    ');
}

require_once __DIR__ . '/../vendor/autoload.php';

// Conectar a la base de datos
$db = new PDO("sqlite:" . __DIR__ . "/../database/wow.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener estadísticas
$totalSesiones = (int) $db->query("SELECT COUNT(*) FROM test_session")->fetchColumn();
$totalTesters = (int) $db->query("SELECT COUNT(*) FROM tester")->fetchColumn();
$totalContenido = (int) $db->query("SELECT COUNT(*) FROM content")->fetchColumn();
$promedioScore = $db->query("SELECT AVG(score) FROM test_session")->fetchColumn();
$promedioScore = $promedioScore !== null ? round($promedioScore, 2) : 0;

// Top 5 Testers
$topTesters = $db->query("
    SELECT t.name, AVG(s.score) as prom, COUNT(*) as total 
    FROM test_session s 
    JOIN tester t ON s.tester=t.id 
    GROUP BY t.name 
    ORDER BY prom DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Contenido más difícil
$hardestContent = $db->query("
    SELECT c.name, AVG(s.score) as prom, COUNT(*) as sesiones
    FROM test_session s 
    JOIN content c ON s.content=c.id 
    GROUP BY c.name 
    ORDER BY prom ASC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Últimas 10 sesiones
$recentSessions = $db->query("
    SELECT s.id, t.name AS tester, c.name AS contenido, 
           s.difficulty, s.score, s.time_played, s.comments
    FROM test_session s
    JOIN tester t ON s.tester = t.id
    JOIN content c ON s.content = c.id
    ORDER BY s.id DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Clase personalizada para el PDF
class WoWReportPDF extends TCPDF
{
    public function Header()
    {
        // Logo WoW (si existe)
        $logoPath = __DIR__ . '/../assets/wow_logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 15, 10, 30);
        }

        // Título
        $this->SetFont('helvetica', 'B', 18);
        $this->SetTextColor(255, 209, 0); // Dorado WoW
        $this->SetY(12);
        $this->Cell(0, 15, 'WoW Test Manager', 0, false, 'C', 0, '', 0, false, 'M', 'M');

        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(180, 180, 180);
        $this->Ln(8);
        $this->Cell(0, 10, 'Reporte de Sesiones de Testing', 0, false, 'C');

        $this->Ln(15);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'C');
    }
}

// Crear instancia del PDF
$pdf = new WoWReportPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Metadatos del documento
$pdf->SetCreator('WoW Test Manager');
$pdf->SetAuthor($_SESSION['user']['username'] ?? 'Admin');
$pdf->SetTitle('Reporte de Sesiones QA - ' . date('d/m/Y'));
$pdf->SetSubject('Estadísticas de Testing');
$pdf->SetKeywords('WoW, Testing, QA, Reporte');

// Configuración
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetMargins(15, 45, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ===== PÁGINA 1: PORTADA Y RESUMEN =====
$pdf->AddPage();

// Fecha y hora
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 8, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
$pdf->Cell(0, 8, 'Por: ' . htmlspecialchars($_SESSION['user']['username'] ?? 'Admin'), 0, 1, 'R');
$pdf->Ln(10);

// Sección: Resumen Ejecutivo
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(255, 209, 0);
$pdf->Cell(0, 10, 'Resumen Ejecutivo', 0, 1, 'L');
$pdf->SetLineStyle(['width' => 0.5, 'color' => [255, 209, 0]]);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(8);

// KPIs en cuadrícula
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(0, 0, 0);

$kpis = [
    ['label' => 'Total de Sesiones', 'value' => $totalSesiones],
    ['label' => 'Testers Activos', 'value' => $totalTesters],
    ['label' => 'Contenidos Probados', 'value' => $totalContenido],
    ['label' => 'Score Promedio Global', 'value' => $promedioScore]
];

$colWidth = 45;
$x = 15;
foreach ($kpis as $i => $kpi) {
    if ($i > 0 && $i % 4 === 0) {
        $pdf->Ln(25);
        $x = 15;
    }

    $pdf->SetXY($x, $pdf->GetY());
    $pdf->SetFillColor(42, 42, 42);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell($colWidth, 8, $kpi['label'], 1, 0, 'C', true);

    $pdf->SetXY($x, $pdf->GetY() + 8);
    $pdf->SetFillColor(250, 250, 250);
    $pdf->SetTextColor(255, 140, 0);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell($colWidth, 10, (string) $kpi['value'], 1, 0, 'C', true);

    $pdf->SetFont('helvetica', 'B', 11);
    $x += $colWidth + 2;
}

$pdf->Ln(20);

// ===== SECCIÓN: TOP 5 TESTERS =====
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(255, 209, 0);
$pdf->Cell(0, 10, 'Top 5 Testers por Calidad', 0, 1, 'L');
$pdf->SetLineStyle(['width' => 0.5, 'color' => [255, 209, 0]]);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

// Cabecera de tabla
$pdf->SetFillColor(42, 42, 42);
$pdf->SetTextColor(255, 209, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(100, 7, 'Tester', 1, 0, 'L', true);
$pdf->Cell(45, 7, 'Score Promedio', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Sesiones', 1, 1, 'C', true);

// Filas de datos
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 9);
foreach ($topTesters as $tester) {
    $pdf->Cell(100, 6, htmlspecialchars($tester['name']), 1, 0, 'L');
    $pdf->SetTextColor(0, 150, 0);
    $pdf->Cell(45, 6, round($tester['prom'], 2), 1, 0, 'C');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(35, 6, $tester['total'], 1, 1, 'C');
}

$pdf->Ln(10);

// ===== SECCIÓN: CONTENIDO MÁS DIFÍCIL =====
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(255, 209, 0);
$pdf->Cell(0, 10, 'Contenido Más Difícil', 0, 1, 'L');
$pdf->SetLineStyle(['width' => 0.5, 'color' => [255, 209, 0]]);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

// Cabecera de tabla
$pdf->SetFillColor(42, 42, 42);
$pdf->SetTextColor(255, 209, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(100, 7, 'Contenido', 1, 0, 'L', true);
$pdf->Cell(45, 7, 'Score Promedio', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Sesiones', 1, 1, 'C', true);

// Filas de datos
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 9);
foreach ($hardestContent as $content) {
    $pdf->Cell(100, 6, htmlspecialchars($content['name']), 1, 0, 'L');
    $pdf->SetTextColor(200, 0, 0);
    $pdf->Cell(45, 6, round($content['prom'], 2), 1, 0, 'C');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(35, 6, $content['sesiones'], 1, 1, 'C');
}

// ===== PÁGINA 2: SESIONES RECIENTES =====
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(255, 209, 0);
$pdf->Cell(0, 10, 'Últimas 10 Sesiones de Testing', 0, 1, 'L');
$pdf->SetLineStyle(['width' => 0.5, 'color' => [255, 209, 0]]);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

// Cabecera de tabla
$pdf->SetFillColor(42, 42, 42);
$pdf->SetTextColor(255, 209, 0);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(12, 7, 'ID', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Tester', 1, 0, 'L', true);
$pdf->Cell(50, 7, 'Contenido', 1, 0, 'L', true);
$pdf->Cell(25, 7, 'Dificultad', 1, 0, 'C', true);
$pdf->Cell(20, 7, 'Score', 1, 0, 'C', true);
$pdf->Cell(33, 7, 'Tiempo', 1, 1, 'C', true);

// Filas de datos
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 8);
foreach ($recentSessions as $session) {
    $pdf->Cell(12, 6, '#' . $session['id'], 1, 0, 'C');
    $pdf->Cell(40, 6, htmlspecialchars(substr($session['tester'], 0, 18)), 1, 0, 'L');
    $pdf->Cell(50, 6, htmlspecialchars(substr($session['contenido'], 0, 22)), 1, 0, 'L');
    $pdf->Cell(25, 6, htmlspecialchars($session['difficulty']), 1, 0, 'C');

    // Color según score
    $score = $session['score'];
    if ($score >= 80) {
        $pdf->SetTextColor(0, 150, 0);
    } elseif ($score >= 50) {
        $pdf->SetTextColor(255, 140, 0);
    } else {
        $pdf->SetTextColor(200, 0, 0);
    }
    $pdf->Cell(20, 6, $score, 1, 0, 'C');

    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(33, 6, htmlspecialchars($session['time_played'] ?? 'N/A'), 1, 1, 'C');
}

$pdf->Ln(10);

// Nota final
$pdf->SetFont('helvetica', 'I', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 5, 'Este reporte ha sido generado automáticamente por WoW Test Manager. Los datos reflejan el estado actual de las sesiones de testing registradas en el sistema.', 0, 'L');

// Generar y descargar el PDF
$filename = 'reporte_wow_' . date('Ymd_His') . '.pdf';
$pdf->Output($filename, 'D');
?>