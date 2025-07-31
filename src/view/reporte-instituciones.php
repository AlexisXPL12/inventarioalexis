<?php
// =================== VALIDACIONES INICIALES ===================
// Verificar que las variables de sesión existan
if (!isset($_SESSION['sesion_id']) || !isset($_SESSION['sesion_token'])) {
    echo "Error: Sesión no válida. Por favor, inicie sesión nuevamente.";
    exit;
}

// Verificar que BASE_URL_SERVER esté definida
if (!defined('BASE_URL_SERVER')) {
    echo "Error: URL del servidor no configurada.";
    exit;
}

// =================== INICIA cURL ===================
$curl = curl_init();

// Preparar los datos POST para la API
$postData = array(
    'sesion' => $_SESSION['sesion_id'],
    'token' => $_SESSION['sesion_token'],
    'pagina' => 1,
    'cantidad_mostrar' => 10000, // Gran cantidad para obtener todos los registros
    'busqueda_tabla_nombre' => '',
    'busqueda_tabla_codigo' => '',
    'busqueda_tabla_ruc' => ''
);

curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Institucion.php?tipo=listar_instituciones_ordenados_tabla",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => http_build_query($postData),
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/x-www-form-urlencoded",
        "Accept: application/json",
        "User-Agent: Sistema-Gestion-Bienes/1.0"
    ),
    // Configuración SSL (si es necesaria)
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    // Configuración adicional para depuración
    CURLOPT_VERBOSE => false
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);
curl_close($curl);

// =================== DEPURACIÓN MEJORADA ===================
if ($err) {
    echo "Error cURL: " . $err;
    echo "<br>URL utilizada: " . BASE_URL_SERVER . "src/control/Institucion.php?tipo=listar_instituciones_ordenados_tabla";
    echo "<br>Datos enviados: " . print_r($postData, true);
    exit;
}

if ($httpCode !== 200) {
    echo "Error HTTP: " . $httpCode;
    echo "<br>Respuesta del servidor: " . $response;
    exit;
}

if (empty($response)) {
    echo "Error: Respuesta vacía del servidor";
    echo "<br>Código HTTP: " . $httpCode;
    exit;
}

// =================== PROCESAMIENTO DE RESPUESTA ===================
// Decodificar la respuesta JSON
$responseData = json_decode($response, true);

// Verificar errores de JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error al decodificar JSON: " . json_last_error_msg();
    echo "<br>Respuesta recibida: " . htmlentities($response);
    exit;
}

// Verificar estructura de la respuesta
if (!$responseData || !$responseData['status']) {
    $errorMsg = isset($responseData['msg']) ? $responseData['msg'] : 'Error desconocido';
    echo "Error del servidor: " . $errorMsg;
    echo "<br>Respuesta completa: " . print_r($responseData, true);
    exit;
}

if (!isset($responseData['contenido']) || !is_array($responseData['contenido'])) {
    echo "Error: No se encontró contenido válido en la respuesta";
    echo "<br>Respuesta completa: " . print_r($responseData, true);
    exit;
}

// =================== GENERAR EXCEL ===================
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Función para insertar imagen desde URL o archivo
function insertImage($worksheet, $imagePath, $column, $row, $width = 100, $height = 60) {
    if (file_exists($imagePath)) {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo institucional');
        $drawing->setPath($imagePath);
        $drawing->setHeight($height);
        $drawing->setWidth($width);
        $drawing->setCoordinates($column . $row);
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($worksheet);
        return true;
    }
    return false;
}

// Crear un nuevo documento
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator("Sistema de Gestión de Bienes - DREA")
    ->setLastModifiedBy("Dirección Regional de Educación de Ayacucho")
    ->setTitle("Reporte de Instituciones Educativas - DREA")
    ->setDescription("Listado completo de instituciones educativas registradas - Gobierno Regional de Ayacucho")
    ->setKeywords("DREA, Instituciones, Educativas, Reporte, Ayacucho")
    ->setCategory("Reportes Institucionales");

$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle('Reporte de Instituciones');

// =================== CREAR HEADER INSTITUCIONAL ===================
$filaInicial = 1;

// Configurar altura de las filas del header
$activeWorksheet->getRowDimension(1)->setRowHeight(25);
$activeWorksheet->getRowDimension(2)->setRowHeight(20);
$activeWorksheet->getRowDimension(3)->setRowHeight(20);
$activeWorksheet->getRowDimension(4)->setRowHeight(20);
$activeWorksheet->getRowDimension(5)->setRowHeight(15);

// Insertar logos si existen los archivos
insertImage($activeWorksheet, './src/assets/drea.webp', 'A', 1, 80, 50);
insertImage($activeWorksheet, './src/assets/dr3.jpg', 'J', 1, 80, 50);

// Header institucional
$activeWorksheet->mergeCells('B1:I1');
$activeWorksheet->setCellValue('B1', 'GOBIERNO REGIONAL DE AYACUCHO');
$activeWorksheet->getStyle('B1')->getFont()
    ->setBold(true)
    ->setSize(14)
    ->setName('Arial');
$activeWorksheet->getStyle('B1')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

$activeWorksheet->mergeCells('B2:I2');
$activeWorksheet->setCellValue('B2', 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO');
$activeWorksheet->getStyle('B2')->getFont()
    ->setBold(true)
    ->setSize(12)
    ->setName('Arial');
$activeWorksheet->getStyle('B2')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

$activeWorksheet->mergeCells('B3:I3');
$activeWorksheet->setCellValue('B3', 'DIRECCIÓN DE ADMINISTRACIÓN');
$activeWorksheet->getStyle('B3')->getFont()
    ->setBold(true)
    ->setSize(11)
    ->setName('Arial');
$activeWorksheet->getStyle('B3')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

// Título del reporte
$activeWorksheet->mergeCells('A4:J4');
$activeWorksheet->setCellValue('A4', 'REPORTE GENERAL DE INSTITUCIONES EDUCATIVAS');
$activeWorksheet->getStyle('A4')->getFont()
    ->setBold(true)
    ->setSize(16)
    ->setName('Arial')
    ->getColor()->setRGB('1F4E79');
$activeWorksheet->getStyle('A4')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

// Línea decorativa
$activeWorksheet->mergeCells('A5:J5');
$activeWorksheet->getStyle('A5:J5')->getBorders()
    ->getBottom()
    ->setBorderStyle(Border::BORDER_THICK)
    ->getColor()->setRGB('1F4E79');

// Fecha y hora de generación
$activeWorksheet->mergeCells('A6:J6');
$activeWorksheet->setCellValue('A6', 'Generado el: ' . date('d/m/Y') . ' a las ' . date('H:i:s'));
$activeWorksheet->getStyle('A6')->getFont()
    ->setSize(10)
    ->setItalic(true)
    ->setName('Arial');
$activeWorksheet->getStyle('A6')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Espacio
$activeWorksheet->getRowDimension(7)->setRowHeight(10);

// =================== ENCABEZADOS DE LA TABLA ===================
$headers = [
    'A' => 'ID',
    'B' => 'Código Modular',
    'C' => 'RUC',
    'D' => 'Nombre de la Institución',
    'E' => 'ID Beneficiario',
    'F' => 'Nombre Beneficiario',
    'G' => 'Correo Beneficiario',
    'H' => 'Teléfono Beneficiario',
    'I' => 'Total Ambientes',
    'J' => 'Total Bienes'
];

$filaEncabezados = 8;
foreach ($headers as $columna => $titulo) {
    $activeWorksheet->setCellValue($columna . $filaEncabezados, $titulo);
    
    // Aplicar estilo profesional a los encabezados
    $activeWorksheet->getStyle($columna . $filaEncabezados)->getFont()
        ->setBold(true)
        ->setSize(10)
        ->setName('Arial')
        ->getColor()->setRGB('FFFFFF');
    
    $activeWorksheet->getStyle($columna . $filaEncabezados)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);
    
    $activeWorksheet->getStyle($columna . $filaEncabezados)->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_MEDIUM)
        ->getColor()->setRGB('1F4E79');
    
    // Color de fondo azul institucional para encabezados
    $activeWorksheet->getStyle($columna . $filaEncabezados)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('1F4E79');
}

$activeWorksheet->getRowDimension($filaEncabezados)->setRowHeight(30);

// =================== LLENAR DATOS DE INSTITUCIONES ===================
$instituciones = $responseData['contenido'] ?? [];
$fila = $filaEncabezados + 1;

foreach ($instituciones as $index => $institucion) {
    // Convertir objeto a array si es necesario
    if (is_object($institucion)) {
        $institucion = (array) $institucion;
    }
    
    // Formatear números
    $totalAmbientes = intval($institucion['total_ambientes'] ?? 0);
    $totalBienes = intval($institucion['total_bienes'] ?? 0);
    
    $activeWorksheet->setCellValue('A' . $fila, $institucion['id'] ?? '');
    $activeWorksheet->setCellValue('B' . $fila, $institucion['cod_modular'] ?? '');
    $activeWorksheet->setCellValue('C' . $fila, $institucion['ruc'] ?? '');
    $activeWorksheet->setCellValue('D' . $fila, $institucion['nombre'] ?? '');
    $activeWorksheet->setCellValue('E' . $fila, $institucion['beneficiario'] ?? '');
    $activeWorksheet->setCellValue('F' . $fila, $institucion['nombre_beneficiario'] ?? '');
    $activeWorksheet->setCellValue('G' . $fila, $institucion['correo_beneficiario'] ?? '');
    $activeWorksheet->setCellValue('H' . $fila, $institucion['telefono_beneficiario'] ?? '');
    $activeWorksheet->setCellValue('I' . $fila, $totalAmbientes);
    $activeWorksheet->setCellValue('J' . $fila, $totalBienes);
    
    // Formatear números con separadores de miles
    $activeWorksheet->getStyle('I' . $fila)->getNumberFormat()
        ->setFormatCode('#,##0');
    $activeWorksheet->getStyle('J' . $fila)->getNumberFormat()
        ->setFormatCode('#,##0');
    
    // Aplicar formato a las celdas de datos
    foreach ($headers as $columna => $titulo) {
        $activeWorksheet->getStyle($columna . $fila)->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('CCCCCC');
        
        $activeWorksheet->getStyle($columna . $fila)->getFont()
            ->setName('Arial')
            ->setSize(9);
        
        // Alineación específica por columna
        if ($columna == 'A' || $columna == 'E' || $columna == 'I' || $columna == 'J') {
            $activeWorksheet->getStyle($columna . $fila)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $activeWorksheet->getStyle($columna . $fila)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        
        $activeWorksheet->getStyle($columna . $fila)->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        
        // Alternar colores de fila para mejor legibilidad
        if ($index % 2 == 0) {
            $activeWorksheet->getStyle($columna . $fila)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F8F9FA');
        }
        
        // Color especial para instituciones con muchos bienes (mayor a 100)
        if ($totalBienes > 100) {
            $activeWorksheet->getStyle($columna . $fila)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E8F5E8');
        }
        
        // Color de alerta para instituciones sin RUC
        if (empty($institucion['ruc'])) {
            $activeWorksheet->getStyle($columna . $fila)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFE6E6');
        }
    }
    
    $activeWorksheet->getRowDimension($fila)->setRowHeight(18);
    $fila++;
}

// =================== RESUMEN ESTADÍSTICO ===================
$filaResumen = $fila + 2;

// Título del resumen
$activeWorksheet->mergeCells('A' . $filaResumen . ':J' . $filaResumen);
$activeWorksheet->setCellValue('A' . $filaResumen, 'RESUMEN ESTADÍSTICO DEL REPORTE');
$activeWorksheet->getStyle('A' . $filaResumen)->getFont()
    ->setBold(true)
    ->setSize(14)
    ->setName('Arial')
    ->getColor()->setRGB('1F4E79');
$activeWorksheet->getStyle('A' . $filaResumen)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
$activeWorksheet->getRowDimension($filaResumen)->setRowHeight(25);

$filaResumen++;

// Calcular estadísticas
$totalInstituciones = count($instituciones);
$totalAmbientesGeneral = array_sum(array_map(function($i) { return intval($i['total_ambientes'] ?? 0); }, $instituciones));
$totalBienesGeneral = array_sum(array_map(function($i) { return intval($i['total_bienes'] ?? 0); }, $instituciones));
$institucionesSinRUC = count(array_filter($instituciones, function($i) { return empty($i['ruc']); }));
$institucionesConMuchosBienes = count(array_filter($instituciones, function($i) { return intval($i['total_bienes'] ?? 0) > 100; }));

$estadisticas = [
    'Total de instituciones registradas:' => $totalInstituciones,
    'Total general de ambientes:' => number_format($totalAmbientesGeneral),
    'Total general de bienes:' => number_format($totalBienesGeneral),
    'Instituciones sin RUC:' => $institucionesSinRUC,
    'Instituciones con más de 100 bienes:' => $institucionesConMuchosBienes,
    'Promedio de bienes por institución:' => $totalInstituciones > 0 ? number_format($totalBienesGeneral / $totalInstituciones, 1) : '0',
    'Promedio de ambientes por institución:' => $totalInstituciones > 0 ? number_format($totalAmbientesGeneral / $totalInstituciones, 1) : '0'
];

foreach ($estadisticas as $concepto => $valor) {
    $activeWorksheet->setCellValue('B' . $filaResumen, $concepto);
    $activeWorksheet->setCellValue('E' . $filaResumen, $valor);
    
    $activeWorksheet->getStyle('B' . $filaResumen)->getFont()
        ->setBold(true)
        ->setName('Arial')
        ->setSize(10);
    
    $activeWorksheet->getStyle('E' . $filaResumen)->getFont()
        ->setName('Arial')
        ->setSize(10);
    
    // Bordes para las estadísticas
    $activeWorksheet->getStyle('B' . $filaResumen . ':E' . $filaResumen)->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);
    
    $filaResumen++;
}

// Separador
$filaResumen++;

// Top 5 instituciones con más bienes
$institucionesOrdenadas = $instituciones;
usort($institucionesOrdenadas, function($a, $b) {
    return intval($b['total_bienes'] ?? 0) - intval($a['total_bienes'] ?? 0);
});

$activeWorksheet->mergeCells('B' . $filaResumen . ':E' . $filaResumen);
$activeWorksheet->setCellValue('B' . $filaResumen, 'TOP 5 INSTITUCIONES CON MÁS BIENES');
$activeWorksheet->getStyle('B' . $filaResumen)->getFont()
    ->setBold(true)
    ->setSize(11)
    ->setName('Arial');
$filaResumen++;

$contador = 1;
foreach (array_slice($institucionesOrdenadas, 0, 5) as $institucion) {
    $nombre = $institucion['nombre'] ?? 'Sin nombre';
    $totalBienes = intval($institucion['total_bienes'] ?? 0);
    
    $activeWorksheet->setCellValue('C' . $filaResumen, $contador . '. ' . substr($nombre, 0, 40) . '...');
    $activeWorksheet->setCellValue('D' . $filaResumen, number_format($totalBienes) . ' bienes');
    $activeWorksheet->getStyle('C' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
    $activeWorksheet->getStyle('D' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
    $filaResumen++;
    $contador++;
}

// =================== AJUSTAR DIMENSIONES ===================
$activeWorksheet->getColumnDimension('A')->setWidth(8);   // ID
$activeWorksheet->getColumnDimension('B')->setWidth(18);  // Código Modular
$activeWorksheet->getColumnDimension('C')->setWidth(15);  // RUC
$activeWorksheet->getColumnDimension('D')->setWidth(40);  // Nombre Institución
$activeWorksheet->getColumnDimension('E')->setWidth(12);  // ID Beneficiario
$activeWorksheet->getColumnDimension('F')->setWidth(30);  // Nombre Beneficiario
$activeWorksheet->getColumnDimension('G')->setWidth(35);  // Correo
$activeWorksheet->getColumnDimension('H')->setWidth(15);  // Teléfono
$activeWorksheet->getColumnDimension('I')->setWidth(12);  // Total Ambientes
$activeWorksheet->getColumnDimension('J')->setWidth(12);  // Total Bienes

// =================== FOOTER INSTITUCIONAL ===================
$filaFooter = $filaResumen + 3;

// Información institucional en el footer
$activeWorksheet->mergeCells('H' . $filaFooter . ':J' . $filaFooter);
$activeWorksheet->setCellValue('H' . $filaFooter, 'www.dreaya.gob.pe');
$activeWorksheet->getStyle('H' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('H' . $filaFooter)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$filaFooter++;
$activeWorksheet->mergeCells('H' . $filaFooter . ':J' . $filaFooter);
$activeWorksheet->setCellValue('H' . $filaFooter, 'Jr. 28 de Julio N° 383 – Huamanga');
$activeWorksheet->getStyle('H' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('H' . $filaFooter)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$filaFooter++;
$activeWorksheet->mergeCells('H' . $filaFooter . ':J' . $filaFooter);
$activeWorksheet->setCellValue('H' . $filaFooter, '(066) 31-1395 Anexo 58001');
$activeWorksheet->getStyle('H' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('H' . $filaFooter)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Configurar márgenes de impresión
$activeWorksheet->getPageMargins()->setTop(0.5);
$activeWorksheet->getPageMargins()->setRight(0.3);
$activeWorksheet->getPageMargins()->setLeft(0.3);
$activeWorksheet->getPageMargins()->setBottom(0.5);

// Configurar orientación y tamaño de página
$activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
$activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

// Configurar headers para descarga directa
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_instituciones_DREA_' . date('Y-m-d_H-i-s') . '.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

// Guardar directamente en la salida (descarga)
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>