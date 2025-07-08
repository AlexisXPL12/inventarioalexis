<?php

$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta[1] == "") {
    header("location: " . BASE_URL . "movimientos");
}

$curl = curl_init(); //inicia la sesión cURL
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] . "&data=" . $ruta[1], //url a la que se conecta
    CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
    CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
    CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
    CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
    CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
    CURLOPT_CUSTOMREQUEST => "GET", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: " . BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ), //configura las cabeceras enviadas al servicio
)); //curl_setopt_array configura las opciones para una transferencia cURL

$response = curl_exec($curl); // respuesta generada
$err = curl_error($curl); // muestra errores en caso de existir

curl_close($curl); // termina la sesión 

if ($err) {
    echo "cURL Error #:" . $err; // mostramos el error
} else {
    $respuesta = json_decode($response);
    //print_r($respuesta); // en caso de funcionar correctamente
    //echo $_SESSION['sesion_sigi_id'];
    //echo $_SESSION['sesion_sigi_token'];
}

?>
<!--
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Papeleta de Rotación de Bienes</title>
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

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 14px;
        }

        .signature {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            text-align: center;
            width: 45%;
        }

        .date {
            text-align: right;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <h2>PAPELETA DE ROTACIÓN DE BIENES</h2>

    <div class="info">
        <p><span class="bold">ENTIDAD</span>: DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO</p>
        <p><span class="bold">ÁREA</span>: OFICINA DE ADMINISTRACIÓN</p>
        <p><span class="bold">ORIGEN</span>:
            <?php echo $respuesta->amb_origen->codigo . " - " . $respuesta->amb_origen->detalle; ?>
        </p>
        <p><span class="bold">DESTINO</span>:
            <?php echo $respuesta->amb_destino->codigo . " - " . $respuesta->amb_destino->detalle; ?>
        </p>
        <p><span class="bold">MOTIVO (*)</span>: <?php echo $respuesta->movimiento->descripcion; ?></p>
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
        <tbody>
            <?php
            if (empty($respuesta->detalle)) {
                echo '<tr><td colspan="7">No se encontraron bienes registrados para este movimiento.</td></tr>';
            } else {
                $contador = 1;
                foreach ($respuesta->detalle as $bien) {
                    echo "<tr>";
                    echo "<td>" . $contador . "</td>";
                    echo "<td>" . $bien->cod_patrimonial . "</td>";
                    echo "<td>" . $bien->denominacion . "</td>";
                    echo "<td>" . $bien->marca . "</td>";
                    echo "<td>" . $bien->color . "</td>";
                    echo "<td>" . $bien->modelo . "</td>";
                    echo "<td>" . $bien->estado_conservacion . "</td>";
                    echo "</tr>";
                    $contador += 1;
                }
            }
            ?>
        </tbody>

    </table>

    <?php
    // Asegura que se use la zona horaria correcta en todo el script
    date_default_timezone_set('America/Lima');

    // Crear el formateador con idioma español y zona horaria Lima
    $fechaMovimiento = new IntlDateFormatter(
        'es_ES',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        'America/Lima',
        IntlDateFormatter::GREGORIAN,
        "d 'de' MMMM 'del' y"
    );

    // Crear el objeto DateTime con zona horaria Lima
    $fechaOriginal = new DateTime($respuesta->movimiento->fecha_registro, new DateTimeZone('America/Lima'));
    $fechaFormateada = $fechaMovimiento->format($fechaOriginal);
    ?>
    <div class="date">
        Ayacucho, <?php echo $fechaFormateada; ?>
    </div>





    <div class="signature">
        <div>
            <p>------------------------------</p>
            <p>ENTREGUE CONFORME</p>
        </div>
        <div>
            <p>------------------------------</p>
            <p>RECIBÍ CONFORME</p>
        </div>
    </div>

</body>

</html>
-->
<?php
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');
// crear nuevo PDF 
$pdf = new TCPDF();

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Alexis Valdivia');
$pdf->SetTitle('Reporte del Movimiento');

//asignar los margenes del documento
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//asignar salto de pagina automatico
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);




