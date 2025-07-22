<?php
// Asegura la zona horaria correcta
date_default_timezone_set('America/Lima');

// Obtener los datos desde el backend por cURL
$curl = curl_init();
$datos_post = http_build_query([
    'ies' => '1' // Reemplaza '1' por el ID real de la institución
]);
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=listar_todos&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: " . BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("Error en cURL: " . $err);
} else {
    $respuesta = json_decode($response, true);
    // Verificar errores en la decodificación JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error al decodificar JSON: " . json_last_error_msg() . ". Respuesta: " . $response);
    }
}
// ----------------------------
// GENERAR EL PDF CON TCPDF
// ----------------------------
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

// Clase personalizada para el PDF
class MYPDF extends TCPDF {
    // Header
    public function Header() {
        $this->Image('./src/assets/drea.webp', 15, 10, 30);
        $this->Image('./src/assets/dr3.jpg', 165, 5, 35);
        $this->SetY(12);
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(0, 5, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');
        $this->Cell(0, 5, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, 1, 'C');
        $this->Cell(0, 5, 'DIRECCIÓN DE ADMINISTRACIÓN', 0, 1, 'C');
    }

    // Footer
    public function Footer() {
        $this->SetY(-25);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 5, 'www.dreaya.gob.pe', 0, 1, 'R');
        $this->Cell(0, 5, 'Jr. 28 de Julio N° 383 – Huamanga', 0, 1, 'R');
        $this->Cell(0, 5, '(066) 31-1395 Anexo 58001', 0, 1, 'R');
    }
}

// Crear nueva instancia del PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración básica
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Alexis Valdivia');
$pdf->SetTitle('Reporte de Movimientos');
$pdf->SetMargins(15, 40, 15); // Ajustado el margen superior
$pdf->SetAutoPageBreak(TRUE, 30); // Espacio desde el pie
$pdf->SetFont('helvetica', '', 10);

// Añadir una página
$pdf->AddPage();

// Contenido HTML para el PDF
$contenido_pdf = '
<h2 style="text-align:center; text-transform:uppercase; color:#2c3e50;">REPORTE DE MOVIMIENTOS</h2>
<div style="margin-bottom: 15px;">
    <p style="margin:6px 0;"><b>ENTIDAD</b>: DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO</p>
    <p style="margin:6px 0;"><b>ÁREA</b>: OFICINA DE ADMINISTRACIÓN</p>
</div>
<table style="width:100%; border-collapse:collapse; margin-top:15px;" border="1" cellpadding="6">
    <thead>
        <tr style="background-color:#eaeaea; color:#2c3e50;">
            <th style="border:1px solid #ccc; font-size:8px; text-align: center;">ITEM</th>
            <th style="border:1px solid #ccc; font-size:8px; text-align: center;">AMBIENTE ORIGEN</th>
            <th style="border:1px solid #ccc; font-size:8px; text-align: center;">AMBIENTE DESTINO</th>
            <th style="border:1px solid #ccc; font-size:8px; text-align: center;">USUARIO REGISTRO</th>
            <th style="border:1px solid #ccc; font-size:8px; text-align: center;">FECHA REGISTRO</th>
            <th style="border:1px solid #ccc; font-size:8px; text-align: center;">DESCRIPCIÓN</th>
        </tr>
    </thead>
    <tbody>';

// Verificar si hay contenido en la respuesta
if (isset($respuesta['contenido']) && !empty($respuesta['contenido'])) {
    $i = 1;
    foreach ($respuesta['contenido'] as $i => $movimiento) {
    $i++;

    // Fecha formateada
    $fechaOriginal = new DateTime($movimiento['movimiento']['fecha_registro'], new DateTimeZone('America/Lima'));
    $fechaMovimiento = new IntlDateFormatter(
        'es_ES',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        'America/Lima',
        IntlDateFormatter::GREGORIAN,
        "d 'de' MMMM 'del' y"
    );
    $fechaFormateada = $fechaMovimiento->format($fechaOriginal);

    $contenido_pdf .= '
    <tr style="background-color:' . ($i % 2 == 0 ? '#f9f9f9' : '#ffffff') . ';">
        <td style="border:1px solid #ccc; font-size:8px;">' . $i . '</td>
        <td style="border:1px solid #ccc; font-size:8px;">' . $movimiento['amb_origen']['detalle']. '</td>
        <td style="border:1px solid #ccc; font-size:8px;">' . $movimiento['amb_destino']['detalle'] . '</td>
        <td style="border:1px solid #ccc; font-size:8px;">' . $movimiento['datos_usuario']['nombres_apellidos']. '</td>
        <td style="border:1px solid #ccc; font-size:8px;">' . $fechaFormateada. '</td>
        <td style="border:1px solid #ccc; font-size:8px;">' . $movimiento['movimiento']['descripcion'] . '</td>
    </tr>';
}

} else {
    $contenido_pdf .= '
    <tr>
        <td colspan="6" style="text-align:center; border:1px solid #ccc; font-size:12.5px;">
            No se encontraron movimientos registrados.
        </td>
    </tr>';
}

$contenido_pdf .= '
    </tbody>
</table>';

// Escribir HTML
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

// Salida del PDF
$pdf->Output('reporte_movimientos.pdf', 'I');
?>
