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
    'busqueda_nombre' => '',
    'busqueda_dni' => '',
    'busqueda_estado' => ''
);

curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Usuario.php?tipo=listar_usuarios_ordenados_tabla_e",
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
    echo "<br>URL utilizada: " . BASE_URL_SERVER . "src/control/Usuario.php?tipo=listar_usuarios_ordenados_tabla_e";
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
    ->setTitle("Reporte de Usuarios del Sistema - DREA")
    ->setDescription("Reporte completo de usuarios registrados en el sistema - Gobierno Regional de Ayacucho")
    ->setKeywords("DREA, Usuarios, Sistema, Administración, Ayacucho")
    ->setCategory("Reportes Administrativos");

$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle('Reporte de Usuarios');

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
insertImage($activeWorksheet, './src/assets/dr3.jpg', 'H', 1, 80, 50);

// Header institucional
$activeWorksheet->mergeCells('B1:G1');
$activeWorksheet->setCellValue('B1', 'GOBIERNO REGIONAL DE AYACUCHO');
$activeWorksheet->getStyle('B1')->getFont()
    ->setBold(true)
    ->setSize(14)
    ->setName('Arial');
$activeWorksheet->getStyle('B1')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

$activeWorksheet->mergeCells('B2:G2');
$activeWorksheet->setCellValue('B2', 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO');
$activeWorksheet->getStyle('B2')->getFont()
    ->setBold(true)
    ->setSize(12)
    ->setName('Arial');
$activeWorksheet->getStyle('B2')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

$activeWorksheet->mergeCells('B3:G3');
$activeWorksheet->setCellValue('B3', 'DIRECCIÓN DE ADMINISTRACIÓN');
$activeWorksheet->getStyle('B3')->getFont()
    ->setBold(true)
    ->setSize(11)
    ->setName('Arial');
$activeWorksheet->getStyle('B3')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

// Título del reporte
$activeWorksheet->mergeCells('A4:H4');
$activeWorksheet->setCellValue('A4', 'REPORTE DE USUARIOS DEL SISTEMA');
$activeWorksheet->getStyle('A4')->getFont()
    ->setBold(true)
    ->setSize(16)
    ->setName('Arial')
    ->getColor()->setRGB('1F4E79');
$activeWorksheet->getStyle('A4')->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

// Línea decorativa
$activeWorksheet->mergeCells('A5:H5');
$activeWorksheet->getStyle('A5:H5')->getBorders()
    ->getBottom()
    ->setBorderStyle(Border::BORDER_THICK)
    ->getColor()->setRGB('1F4E79');

// Fecha y hora de generación
$activeWorksheet->mergeCells('A6:H6');
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
    'B' => 'DNI',
    'C' => 'Nombres y Apellidos',
    'D' => 'Correo Electrónico',
    'E' => 'Teléfono',
    'F' => 'Estado',
    'G' => 'Fecha de Registro',
    'H' => 'Último Acceso'
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

// =================== LLENAR DATOS DE USUARIOS ===================
$usuarios = $responseData['contenido'] ?? [];
$fila = $filaEncabezados + 1;

foreach ($usuarios as $index => $usuario) {
    // Convertir objeto a array si es necesario
    if (is_object($usuario)) {
        $usuario = (array) $usuario;
    }
    
    if (!is_array($usuario)) {
        continue; // Saltar si no es un array válido
    }
    
    // Estado texto legible
    $estadoTexto = (isset($usuario['estado']) && $usuario['estado'] == 1) ? 'Activo' : 'Inactivo';
    
    // Formatear fecha de registro
    $fechaRegistro = '';
    if (!empty($usuario['fecha_registro'])) {
        try {
            $fechaRegistro = date('d/m/Y H:i:s', strtotime($usuario['fecha_registro']));
        } catch (Exception $e) {
            $fechaRegistro = $usuario['fecha_registro']; // Usar valor original si hay error
        }
    }
    
    // Manejar último acceso
    $ultimoAcceso = 'Sin accesos';
    if (!empty($usuario['ultimo_acceso']) && $usuario['ultimo_acceso'] !== 'Sin accesos') {
        try {
            $ultimoAcceso = date('d/m/Y H:i:s', strtotime($usuario['ultimo_acceso']));
        } catch (Exception $e) {
            $ultimoAcceso = $usuario['ultimo_acceso'];
        }
    }
    
    $activeWorksheet->setCellValue('A' . $fila, $usuario['id'] ?? '');
    $activeWorksheet->setCellValue('B' . $fila, $usuario['dni'] ?? '');
    $activeWorksheet->setCellValue('C' . $fila, $usuario['nombres_apellidos'] ?? '');
    $activeWorksheet->setCellValue('D' . $fila, $usuario['correo'] ?? '');
    $activeWorksheet->setCellValue('E' . $fila, $usuario['telefono'] ?? '');
    $activeWorksheet->setCellValue('F' . $fila, $estadoTexto);
    $activeWorksheet->setCellValue('G' . $fila, $fechaRegistro);
    $activeWorksheet->setCellValue('H' . $fila, $ultimoAcceso);
    
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
        if ($columna == 'A' || $columna == 'F') {
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
        
        // Color especial para usuarios activos
        if (isset($usuario['estado']) && $usuario['estado'] == 1) {
            $activeWorksheet->getStyle($columna . $fila)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E8F5E8');
        }
        
        // Color de alerta para usuarios inactivos
        if (isset($usuario['estado']) && $usuario['estado'] == 0) {
            $activeWorksheet->getStyle($columna . $fila)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFE6E6');
        }
        
        // Color especial para usuarios sin accesos recientes
        if ($ultimoAcceso === 'Sin accesos') {
            $activeWorksheet->getStyle($columna . $fila)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFF8E1');
        }
    }
    
    $activeWorksheet->getRowDimension($fila)->setRowHeight(18);
    $fila++;
}

// =================== RESUMEN ESTADÍSTICO ===================
$filaResumen = $fila + 2;

// Título del resumen
$activeWorksheet->mergeCells('A' . $filaResumen . ':H' . $filaResumen);
$activeWorksheet->setCellValue('A' . $filaResumen, 'RESUMEN ESTADÍSTICO DE USUARIOS');
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
$totalUsuarios = count($usuarios);
$usuariosActivos = count(array_filter($usuarios, function($u) { 
    $usuario = is_object($u) ? (array) $u : $u;
    return isset($usuario['estado']) && $usuario['estado'] == 1; 
}));
$usuariosInactivos = $totalUsuarios - $usuariosActivos;
$usuariosSinAcceso = count(array_filter($usuarios, function($u) { 
    $usuario = is_object($u) ? (array) $u : $u;
    return empty($usuario['ultimo_acceso']) || $usuario['ultimo_acceso'] === 'Sin accesos';
}));

// Usuarios registrados en los últimos 30 días
$hace30Dias = strtotime('-30 days');
$usuariosRecientes = count(array_filter($usuarios, function($u) use ($hace30Dias) { 
    $usuario = is_object($u) ? (array) $u : $u;
    if (empty($usuario['fecha_registro'])) return false;
    $fechaRegistro = strtotime($usuario['fecha_registro']);
    return $fechaRegistro && $fechaRegistro > $hace30Dias;
}));

$estadisticas = [
    'Total de usuarios registrados:' => $totalUsuarios,
    'Usuarios activos:' => $usuariosActivos,
    'Usuarios inactivos:' => $usuariosInactivos,
    'Usuarios sin accesos registrados:' => $usuariosSinAcceso,
    'Usuarios registrados últimos 30 días:' => $usuariosRecientes
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

// Distribución por estado
$activeWorksheet->mergeCells('B' . $filaResumen . ':E' . $filaResumen);
$activeWorksheet->setCellValue('B' . $filaResumen, 'DISTRIBUCIÓN POR ESTADO');
$activeWorksheet->getStyle('B' . $filaResumen)->getFont()
    ->setBold(true)
    ->setSize(11)
    ->setName('Arial');
$filaResumen++;

$porcentajeActivos = $totalUsuarios > 0 ? round(($usuariosActivos / $totalUsuarios) * 100, 2) : 0;
$porcentajeInactivos = $totalUsuarios > 0 ? round(($usuariosInactivos / $totalUsuarios) * 100, 2) : 0;

$activeWorksheet->setCellValue('C' . $filaResumen, 'Usuarios Activos:');
$activeWorksheet->setCellValue('D' . $filaResumen, $usuariosActivos . ' (' . $porcentajeActivos . '%)');
$activeWorksheet->getStyle('C' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
$activeWorksheet->getStyle('D' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
$filaResumen++;

$activeWorksheet->setCellValue('C' . $filaResumen, 'Usuarios Inactivos:');
$activeWorksheet->setCellValue('D' . $filaResumen, $usuariosInactivos . ' (' . $porcentajeInactivos . '%)');
$activeWorksheet->getStyle('C' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
$activeWorksheet->getStyle('D' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
$filaResumen++;

// Separador
$filaResumen++;

// Estadísticas de accesos
$activeWorksheet->mergeCells('B' . $filaResumen . ':E' . $filaResumen);
$activeWorksheet->setCellValue('B' . $filaResumen, 'ESTADÍSTICAS DE ACCESOS');
$activeWorksheet->getStyle('B' . $filaResumen)->getFont()
    ->setBold(true)
    ->setSize(11)
    ->setName('Arial');
$filaResumen++;

$usuariosConAcceso = $totalUsuarios - $usuariosSinAcceso;
$porcentajeSinAcceso = $totalUsuarios > 0 ? round(($usuariosSinAcceso / $totalUsuarios) * 100, 2) : 0;

$activeWorksheet->setCellValue('C' . $filaResumen, 'Con accesos registrados:');
$activeWorksheet->setCellValue('D' . $filaResumen, $usuariosConAcceso);
$activeWorksheet->getStyle('C' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
$activeWorksheet->getStyle('D' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
$filaResumen++;

$activeWorksheet->setCellValue('C' . $filaResumen, 'Sin accesos registrados:');
$activeWorksheet->setCellValue('D' . $filaResumen, $usuariosSinAcceso . ' (' . $porcentajeSinAcceso . '%)');
$activeWorksheet->getStyle('C' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
$activeWorksheet->getStyle('D' . $filaResumen)->getFont()->setName('Arial')->setSize(9);
$filaResumen++;

// =================== AJUSTAR DIMENSIONES ===================
$activeWorksheet->getColumnDimension('A')->setWidth(8);   // ID
$activeWorksheet->getColumnDimension('B')->setWidth(15);  // DNI
$activeWorksheet->getColumnDimension('C')->setWidth(30);  // Nombres y Apellidos
$activeWorksheet->getColumnDimension('D')->setWidth(35);  // Correo Electrónico
$activeWorksheet->getColumnDimension('E')->setWidth(15);  // Teléfono
$activeWorksheet->getColumnDimension('F')->setWidth(12);  // Estado
$activeWorksheet->getColumnDimension('G')->setWidth(20);  // Fecha de Registro
$activeWorksheet->getColumnDimension('H')->setWidth(20);  // Último Acceso

// =================== FOOTER INSTITUCIONAL ===================
$filaFooter = $filaResumen + 3;

// Información institucional en el footer
$activeWorksheet->mergeCells('F' . $filaFooter . ':H' . $filaFooter);
$activeWorksheet->setCellValue('F' . $filaFooter, 'www.dreaya.gob.pe');
$activeWorksheet->getStyle('F' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('F' . $filaFooter)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$filaFooter++;
$activeWorksheet->mergeCells('F' . $filaFooter . ':H' . $filaFooter);
$activeWorksheet->setCellValue('F' . $filaFooter, 'Jr. 28 de Julio N° 383 – Huamanga');
$activeWorksheet->getStyle('F' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('F' . $filaFooter)->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$filaFooter++;
$activeWorksheet->mergeCells('F' . $filaFooter . ':H' . $filaFooter);
$activeWorksheet->setCellValue('F' . $filaFooter, '(066) 31-1395 Anexo 58001');
$activeWorksheet->getStyle('F' . $filaFooter)->getFont()
    ->setSize(9)
    ->setName('Arial')
    ->setItalic(true);
$activeWorksheet->getStyle('F' . $filaFooter)->getAlignment()
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
header('Content-Disposition: attachment;filename="reporte_usuarios_DREA_' . date('Y-m-d_H-i-s') . '.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

// Guardar directamente en la salida (descarga)
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>