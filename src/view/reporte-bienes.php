<?php
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Configuración inicial
date_default_timezone_set('America/Lima');

// Obtener los datos desde el backend por cURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=listar_todos_bienes&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'],
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

// Crear una nueva instancia de Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getProperties()
    ->setCreator("Alexis Valdivia")
    ->setLastModifiedBy("Alexis Valdivia")
    ->setTitle("Reporte de Bienes")
    ->setDescription("Reporte de bienes generado automáticamente");

// Configurar los encabezados
$sheet->setCellValue('A1', 'ITEM');
$sheet->setCellValue('B1', 'CÓDIGO PATRIMONIAL');
$sheet->setCellValue('C1', 'DENOMINACIÓN');
$sheet->setCellValue('D1', 'MARCA');
$sheet->setCellValue('E1', 'MODELO');
$sheet->setCellValue('F1', 'AMBIENTE');

// Verificar si hay contenido en la respuesta
if (isset($respuesta['contenido']) && !empty($respuesta['contenido'])) {
    $fila = 2; // Comenzar desde la segunda fila
    foreach ($respuesta['contenido'] as $i => $bien) {
        $sheet->setCellValue('A' . $fila, $i + 1);
        $sheet->setCellValue('B' . $fila, $bien['cod_patrimonial']);
        $sheet->setCellValue('C' . $fila, $bien['denominacion']);
        $sheet->setCellValue('D' . $fila, $bien['marca']);
        $sheet->setCellValue('E' . $fila, $bien['modelo']);
        $sheet->setCellValue('F' . $fila, $bien['ambiente_detalle']);
        $fila++;
    }
} else {
    $sheet->setCellValue('A2', 'No se encontraron bienes registrados.');
}

// Crear el writer para guardar el archivo Excel
$writer = new Xlsx($spreadsheet);

// Guardar el archivo Excel
$filename = 'reporte_bienes.xlsx';
$writer->save($filename);

// Enviar el archivo al navegador para su descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;


