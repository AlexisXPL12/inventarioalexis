<?php
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// 1. Crear libro y hoja activa
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getProperties()->setCreator("Alexis Valdivia")->setLastModifiedBy("Alexis Valdivia")->setTitle("Alexis Valdivia")->setDescription("Alexis Valdivia");
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle('Hoja 1');



for ($fila = 1; $fila <= 12; $fila++) {
    $activeWorksheet->setCellValue('A' . $fila, '1');
    $activeWorksheet->setCellValue('A' . $fila, '2');
    $activeWorksheet->setCellValue('B' . $fila, 'x');
    $activeWorksheet->setCellValue('C' . $fila, $fila);
    $activeWorksheet->setCellValue('D' . $fila, '=');
    $activeWorksheet->setCellValue('E' . $fila, $fila * 2);

}


// 2. Escribir contenido
$whiter =new XLsx($spreadsheet);
$whiter->save('hello world.xlsx');
