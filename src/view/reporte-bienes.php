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
    'ies' => $_SESSION['ies'] ?? 1, // ID de la institución desde la sesión
    'pagina' => 1,
    'cantidad_mostrar' => 10000, // Gran cantidad para obtener todos los registros
    'busqueda_tabla_codigo' => '',
    'busqueda_tabla_ambiente' => '',
    'busqueda_tabla_denominacion' => ''
);

curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Bien.php?tipo=listar_bienes_ordenados_tabla",
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
    echo "<br>URL utilizada: " . BASE_URL_SERVER . "src/control/Bien.php?tipo=listar_bienes_ordenados_tabla";
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
    ->setTitle("Inventario de Bienes Patrimoniales - DREA")
    ->setDescription("Inventario completo de bienes patrimoniales registrados - Gobierno Regional de Ayacucho")
    ->setKeywords("DREA, Bienes, Inventario, Patrimonio, Ayacucho")
    ->setCategory("Reportes Patrimoniales");

$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle('Inventario de Bienes');

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
insertImage($activeWorksheet, './src/assets/dr3.jpg', 'N', 1, 80, 50);

// Header institucional
$activeWorksheet->mergeCells('B1:M1');
$activeWorksheet->setCellValue('B1', 'GOBIERNO REGIONAL DE AYACUCHO');
$activeWorksheet->getStyle('B1')->getFont()
    ->setBold(true)
    ->setSize(14)
    ->setName('Arial');
$activeWorksheet->getStyle('B1')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

$activeWorksheet->mergeCells('B2:M2');
$activeWorksheet->setCellValue('B2', 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO');
$activeWorksheet->getStyle('B2')->getFont()
    ->setBold(true)
    ->setSize(12)
    ->setName('Arial');
$activeWorksheet->getStyle('B2')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

$activeWorksheet->mergeCells('B3:M3');
$activeWorksheet->setCellValue('B3', 'DIRECCIÓN DE ADMINISTRACIÓN');
$activeWorksheet->getStyle('B3')->getFont()
    ->setBold(true)
    ->setSize(11)
    ->setName('Arial');
$activeWorksheet->getStyle('B3')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

// Título del reporte
$activeWorksheet->mergeCells('A4:N4');
$activeWorksheet->setCellValue('A4', 'INVENTARIO GENERAL DE BIENES PATRIMONIALES');
$activeWorksheet->getStyle('A4')->getFont()
    ->setBold(true)
    ->setSize(16)
    ->setName('Arial')
    ->getColor()->setRGB('1F4E79');
$activeWorksheet->getStyle('A4')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

// Línea decorativa
$activeWorksheet->mergeCells('A5:N5');
$activeWorksheet->getStyle('A5:N5')->getBorders()
    ->getBottom()
    ->setBorderStyle(Border::BORDER_THICK)
    ->getColor()->setRGB('1F4E79');

// Fecha y hora de generación
$activeWorksheet->mergeCells('A6:N6');
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
    'B' => 'Código Patrimonial',
    'C' => 'Denominación',
    'D' => 'Marca',
    'E' => 'Modelo',
    'F' => 'Tipo',
    'G' => 'Color',
    'H' => 'Serie',
    'I' => 'Dimensiones',
    'J' => 'Valor (S/.)',
    'K' => 'Situación',
    'L' => 'Estado de Conservación',
    'M' => 'Observaciones',
    'N' => 'ID Ambiente'
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

// =================== LLENAR DATOS DE BIENES ===================
$bienes = $responseData['contenido'] ?? [];
$fila = $filaEncabezados + 1;

foreach ($bienes as $index => $bien) {
    // Convertir objeto a array si es necesario
    if (is_object($bien)) {
        $bien = (array) $bien;
    }
    
    // Formatear valor como número
    $valor = floatval($bien['valor'] ?? 0);
    
    $activeWorksheet->setCellValue('A' . $fila, $bien['id'] ?? '');
    $activeWorksheet->setCellValue('B' . $fila, $bien['cod_patrimonial'] ?? '');
    $activeWorksheet->setCellValue('C' . $fila, $bien['denominacion'] ?? '');
    $activeWorksheet->setCellValue('D' . $fila, $bien['marca'] ?? '');
    $activeWorksheet->setCellValue('E' . $fila, $bien['modelo'] ?? '');
    $activeWorksheet->setCellValue('F' . $fila, $bien['tipo'] ?? '');
    $activeWorksheet->setCellValue('G' . $fila, $bien['color'] ?? '');
    $activeWorksheet->setCellValue('H' . $fila, $bien['serie'] ?? '');
    $activeWorksheet->setCellValue('I' . $fila, $bien['dimensiones'] ?? '');
    $activeWorksheet->setCellValue('J' . $fila, $valor);
    $activeWorksheet->setCellValue('K' . $fila, $bien['situacion'] ?? '');
    $activeWorksheet->setCellValue('L' . $fila, $bien['estado_conservacion'] ?? '');
    $activeWorksheet->setCellValue('M' . $fila, $bien['observaciones'] ?? '');
    $activeWorksheet->setCellValue('N' . $fila, $bien['id_ambiente'] ?? '');
    
    // Formatear valor como moneda
    $activeWorksheet->getStyle('J' . $fila)->getNumberFormat()
        ->setFormatCode('#,##0.00');
    
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
        if ($columna == 'A' || $columna == 'J' || $columna == 'N') {
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
        
        // Color especial para bienes con valor alto (mayor a 1000)
        if ($valor > 1000) {
            $activeWorksheet->getStyle($columna . $fila)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E8F5E8');
        }
        
        // Color de alerta para bienes sin código patrimonial
        if (empty($bien['cod_patrimonial'])) {
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
$activeWorksheet->mergeCells('A' . $filaResumen . ':N' . $filaResumen);
$activeWorksheet->setCellValue('A' . $filaResumen, 'RESUMEN ESTADÍSTICO DEL INVENTARIO');
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
$totalBienes = count($bienes);
$valorTotal = array_sum(array_map(function($b) { return floatval($b['valor'] ?? 0); }, $bienes));
$bienesSinCodigo = count(array_filter($bienes, function($b) { return empty($b['cod_patrimonial']); }));
$bienesAltoValor = count(array_filter($bienes, function($b) { return floatval($b['valor'] ?? 0) > 1000; }));

// Agrupar por estado de conservación
$estadosConservacion = array_count_values(array_column($bienes, 'estado_conservacion'));

// Agrupar por situación
$situaciones = array_count_values(array_column($bienes, 'situacion'));

$estadisticas = [
    'Total de bienes inventariados:' => $totalBienes,
    'Valor total del inventario:' => 'S/. ' . number_format($valorTotal, 2),
    'Bienes sin código patrimonial:' => $bienesSinCodigo,
    'Bienes de alto valor (>S/. 1,000):' => $bienesAltoValor,
    'Valor promedio por bien:' => 'S/. ' . number_format($totalBienes > 0 ? $valorTotal / $totalBienes : 0, 2)
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

// Estados de conservación
if (!empty($estadosConservacion)) {
    $activeWorksheet->mergeCells('B' . $filaResumen . ':E' . $filaResumen);
    $activeWorksheet->setCellValue('B' . $filaResumen, 'DISTRIBUCIÓN POR ESTADO DE CONSERVACIÓN');
    $activeWorksheet->getStyle('B' . $filaResumen)->getFont()
        ->setBold(true)
        ->setSize(11)
        ->setName('Arial');
    $filaResumen++;
    
    foreach ($estadosConservacion as $estado => $cantidad) {
        if (!empty($estado)) {
            $activeWorksheet->setCellValue('C' . $filaResumen, $estado . ':');
            $activeWorksheet->setCellValue('D' . $filaResumen, $cantidad);
            $activeWorksheet->getStyle('C' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
            $activeWorksheet->getStyle('D' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
            $filaResumen++;
        }
    }
}

// Separador
$filaResumen++;

// Situaciones
if (!empty($situaciones)) {
    $activeWorksheet->mergeCells('B' . $filaResumen . ':E' . $filaResumen);
    $activeWorksheet->setCellValue('B' . $filaResumen, 'DISTRIBUCIÓN POR SITUACIÓN');
    $activeWorksheet->getStyle('B' . $filaResumen)->getFont()
        ->setBold(true)
        ->setSize(11)
        ->setName('Arial');
    $filaResumen++;
    
    foreach ($situaciones as $situacion => $cantidad) {
        if (!empty($situacion)) {
            $activeWorksheet->setCellValue('C' . $filaResumen, $situacion . ':');
            $activeWorksheet->setCellValue('D' . $filaResumen, $cantidad);
            $activeWorksheet->getStyle('C' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
            $activeWorksheet->getStyle('D' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
            $filaResumen++;
        }
    }
}

// =================== AJUSTAR DIMENSIONES ===================
$activeWorksheet->getColumnDimension('A')->setWidth(6);   // ID
$activeWorksheet->getColumnDimension('B')->setWidth(18);  // Código Patrimonial
$activeWorksheet->getColumnDimension('C')->setWidth(30);  // Denominación
$activeWorksheet->getColumnDimension('D')->setWidth(12);  // Marca
$activeWorksheet->getColumnDimension('E')->setWidth(12);  // Modelo
$activeWorksheet->getColumnDimension('F')->setWidth(10);  // Tipo
$activeWorksheet->getColumnDimension('G')->setWidth(8);   // Color
$activeWorksheet->getColumnDimension('H')->setWidth(12);  // Serie
$activeWorksheet->getColumnDimension('I')->setWidth(12);  // Dimensiones
$activeWorksheet->getColumnDimension('J')->setWidth(12);  // Valor
$activeWorksheet->getColumnDimension('K')->setWidth(10);  // Situación
$activeWorksheet->getColumnDimension('L')->setWidth(15);  // Estado Conservación
$activeWorksheet->getColumnDimension('M')->setWidth(25);  // Observaciones
$activeWorksheet->getColumnDimension('N')->setWidth(8);   // ID Ambiente

// =================== FOOTER INSTITUCIONAL ===================
$filaFooter = $filaResumen + 3;

// Información institucional en el footer
$activeWorksheet->mergeCells('L' . $filaFooter . ':N' . $filaFooter);
$activeWorksheet->setCellValue('L' . $filaFooter, 'www.dreaya.gob.pe');
$activeWorksheet->getStyle('L' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('L' . $filaFooter)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$filaFooter++;
$activeWorksheet->mergeCells('L' . $filaFooter . ':N' . $filaFooter);
$activeWorksheet->setCellValue('L' . $filaFooter, 'Jr. 28 de Julio N° 383 – Huamanga');
$activeWorksheet->getStyle('L' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('L' . $filaFooter)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$filaFooter++;
$activeWorksheet->mergeCells('L' . $filaFooter . ':N' . $filaFooter);
$activeWorksheet->setCellValue('L' . $filaFooter, '(066) 31-1395 Anexo 58001');
$activeWorksheet->getStyle('L' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('L' . $filaFooter)->getAlignment()
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
header('Content-Disposition: attachment;filename="inventario_bienes_DREA_' . date('Y-m-d_H-i-s') . '.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

// Guardar directamente en la salida (descarga)
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>