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

// Comenzar contenido HTML para el PDF
$contenido_pdf = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimientos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
        }

        .info {
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .info p {
            margin: 8px 0;
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
            background-color: #f2f2f2;
            border: 1px solid #999;
            padding: 8px;
            font-size: 14px;
        }

        td {
            border: 1px solid #999;
            padding: 6px;
            font-size: 13px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .date {
            text-align: right;
            margin-top: 30px;
            font-size: 14px;
        }

        .signature {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            text-align: center;
            width: 45%;
        }

        hr {
            margin-top: 40px;
            border: 0;
            border-top: 1px solid #aaa;
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
    $contenido_pdf .= '<tr><td colspan="7">No se encontraron bienes registrados para este movimiento.</td></tr>';
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

    <div class="signature">
        <div>
            <p>------------------------------</p>
            <p>ENTREGUÉ CONFORME</p>
        </div>
        <div>
            <p>------------------------------</p>
            <p>RECIBÍ CONFORME</p>
        </div>
    </div>

</body>
</html>';


// ----------------------------
// GENERAR EL PDF CON TCPDF
// ----------------------------
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Alexis Valdivia');
$pdf->SetTitle('Reporte de Movimientos');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

// Escribir HTML al PDF
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

$pdf->Output('reporte_movimiento.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+







