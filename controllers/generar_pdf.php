<?php
require_once __DIR__ . '/../models/Partido.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;

$fecha = $_GET['fecha'] ?? date('Y-m-d');
$partidos = Partido::obtenerPorFecha($fecha);

$html = "<h1>Partidos para el día " . date('d/m/Y', strtotime($fecha)) . "</h1>";

if (empty($partidos)) {
    $html .= "<p>No hay partidos programados para esta fecha.</p>";
} else {
    foreach ($partidos as $partido) {
        $html .= "<hr>";
        $html .= "<h2>" . htmlspecialchars($partido['equipo_local']) . " vs " . htmlspecialchars($partido['equipo_visitante']) . "</h2>";
        $html .= "<p><strong>Fecha:</strong> " . date('d/m/Y', strtotime($partido['fecha'])) . " a las " . date('H:i', strtotime($partido['hora'])) . "</p>";
        $html .= "<p><strong>Estadio:</strong> " . htmlspecialchars($partido['estadio']) . "</p>";
        $html .= "<p><strong>Árbitro:</strong> " . htmlspecialchars($partido['arbitro']) . "</p>";
        $html .= "<p><strong>Resultado:</strong> " . ($partido['resultado_local'] ?? '-') . " - " . ($partido['resultado_visitante'] ?? '-') . "</p>";
    }
}

// Crear PDF con Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("partidos_" . $fecha . ".pdf", ["Attachment" => false]); // true si quieres que lo descargue directamente
