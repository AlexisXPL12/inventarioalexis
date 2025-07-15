<?php
// Asegura la zona horaria correcta
date_default_timezone_set('America/Lima');

// Obtener ID de movimiento desde la URL
$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta[1] == "") {
    header("location: " . BASE_URL . "movimientos");
    exit;
}

// Obtener los datos desde el backend por cURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] . "&data=" . $ruta[1],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
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
    $respuesta = json_decode($response);
}

// Formatear la fecha del movimiento
$fechaMovimiento = new IntlDateFormatter(
    'es_ES',
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'America/Lima',
    IntlDateFormatter::GREGORIAN,
    "d 'de' MMMM 'del' y"
);
$fechaOriginal = new DateTime($respuesta->movimiento->fecha_registro, new DateTimeZone('America/Lima'));
$fechaFormateada = $fechaMovimiento->format($fechaOriginal);

$contenido_pdf = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimientos</title>
    <style>
        body {
            font-family: "Helvetica", Arial, sans-serif;
            font-size: 13px;
            color: #333;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            color: #2c3e50;
        }

        .info {
            margin-bottom: 15px;
        }

        .info p {
            margin: 6px 0;
        }

        .bold {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #eaeaea;
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 13px;
            color: #2c3e50;
        }

        td {
            border: 1px solid #ccc;
            padding: 6px;
            font-size: 12.5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .date {
            text-align: right;
            margin-top: 35px;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <h2>REPORTE DE MOVIMIENTOS</h2>

    <div class="info">
        <p><span class="bold">ENTIDAD</span>: DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO</p>
        <p><span class="bold">ÁREA</span>: OFICINA DE ADMINISTRACIÓN</p>
        <p><span class="bold">ORIGEN</span>: ' . $respuesta->amb_origen->codigo . ' - ' . $respuesta->amb_origen->detalle . '</p>
        <p><span class="bold">DESTINO</span>: ' . $respuesta->amb_destino->codigo . ' - ' . $respuesta->amb_destino->detalle . '</p>
        <p><span class="bold">MOTIVO (*)</span>: ' . $respuesta->movimiento->descripcion . '</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ITEM</th>
                <th>CÓDIGO PATRIMONIAL</th>
                <th>NOMBRE DEL BIEN</th>
                <th>MARCA</th>
                <th>COLOR</th>
                <th>MODELO</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>';

if (empty($respuesta->detalle)) {
    $contenido_pdf .= '<tr><td colspan="7" style="text-align:center;">No se encontraron bienes registrados para este movimiento.</td></tr>';
} else {
    $contador = 1;
    foreach ($respuesta->detalle as $bien) {
        $contenido_pdf .= '<tr>';
        $contenido_pdf .= '<td>' . $contador++ . '</td>';
        $contenido_pdf .= '<td>' . $bien->cod_patrimonial . '</td>';
        $contenido_pdf .= '<td>' . $bien->denominacion . '</td>';
        $contenido_pdf .= '<td>' . $bien->marca . '</td>';
        $contenido_pdf .= '<td>' . $bien->color . '</td>';
        $contenido_pdf .= '<td>' . $bien->modelo . '</td>';
        $contenido_pdf .= '<td>' . $bien->estado_conservacion . '</td>';
        $contenido_pdf .= '</tr>';
    }
}

$contenido_pdf .= '
        </tbody>
    </table>

    <div class="date">
        Ayacucho, ' . $fechaFormateada . '
    </div>

    <div class="signature" style="margin-top: 60px; display: flex; justify-content: space-between; align-items: center;">
        <div style="text-align: center; width: 45%;">
            <p>__________________________</p>
            <p>ENTREGUÉ CONFORME</p>
        </div>
        <div style="text-align: center; width: 45%;">
            <p>__________________________</p>
            <p>RECIBÍ CONFORME</p>
        </div>
    </div>

</body>
</html>';



// ----------------------------
// GENERAR EL PDF CON TCPDF
// ----------------------------
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    // Header personalizado
    public function Header() {
        // Establecer posición vertical desde el borde superior
        $this->SetY(10);

        // Agregar imágenes (logotipos)
        $this->Image('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT72gURRvO9EMLPg4EM7_0Ttl2u52Xigbe6IA&s', 15, 10, 20, '', '', '', '', false, 300);
        $this->Image('https://dreayacucho.gob.pe/storage/directory/ZOOEA2msQPiXYkJFx4JLjpoREncLFn-metabG9nby5wbmc=-.webp', 175, 10, 20, '', '', '', '', false, 300);

        // Texto centrado
        $this->SetFont('helvetica', 'B', 10);
        $this->SetY(12);
        $this->Cell(0, 5, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');
        $this->Cell(0, 5, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, 1, 'C');
        $this->Cell(0, 5, 'DIRECCIÓN DE ADMINISTRACIÓN', 0, 1, 'C');
    }

    // Footer personalizado
    public function Footer() {
        $this->SetY(-28); // Posición desde el fondo
        $this->SetFont('helvetica', '', 8);
        
        $this->Cell(0, 5, 'www.dreaya.gob.pe', 0, 1, 'R', 0, 'http://www.dreaya.gob.pe');
        $this->Cell(0, 5, 'Jr. 28 de Julio N° 383 – Huamanga', 0, 1, 'R');
        $this->Cell(0, 5, '(066) 31-1395 Anexo 58001', 0, 1, 'R');
    }
}

// Crear nueva instancia del PDF personalizado
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración básica
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Alexis Valdivia');
$pdf->SetTitle('Reporte de Movimientos');

// Márgenes: izquierda, top, derecha
$pdf->SetMargins(15, 10, 15); // Ajustado el margen superior
$pdf->SetAutoPageBreak(TRUE, 35); // Espacio desde el pie
$pdf->SetFont('helvetica', '', 10);

// Añadir una página
$pdf->AddPage();

// Escribir HTML
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

// Salida del PDF
$pdf->Output('reporte_movimiento.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+







