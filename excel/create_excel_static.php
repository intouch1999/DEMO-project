<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new spreadsheet object
$spreadsheet = new Spreadsheet();

// Set the active sheet and retrieve it
$sheet = $spreadsheet->getActiveSheet();

// Add some data to the spreadsheet
$sheet->setCellValue('A1', 'Name คนโดนไล่ออก');
$sheet->setCellValue('B1', 'Age');
$sheet->setCellValue('C1', 'Email');

$sheet->setCellValue('A2', 'ไอซ์');
$sheet->setCellValue('B2', 30);
$sheet->setCellValue('C2', 'john.doe@example.com');

$sheet->setCellValue('A3', 'Jane Smith');
$sheet->setCellValue('B3', 25);
$sheet->setCellValue('C3', 'jane.smith@example.com');

// Set the HTTP headers to prompt a file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ไล่ออก.xlsx"');
header('Cache-Control: max-age=0');

// Write the spreadsheet to the output buffer
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
?>
