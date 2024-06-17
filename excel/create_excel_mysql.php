<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include('connection.php');

// Create a new spreadsheet object
$spreadsheet = new Spreadsheet();

// Set the active sheet and retrieve it
$sheet = $spreadsheet->getActiveSheet();

// Add column headers to the spreadsheet
$sheet->setCellValue('A1', 'รหัสสินค้า');
$sheet->setCellValue('B1', 'item_name');
$sheet->setCellValue('C1', 'item_price');
$sheet->setCellValue('D1', 'item_interest');
$sheet->setCellValue('E1', 'item_d_create');


$sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');


// Fetch data from the database and add it to the spreadsheet
$query = "SELECT `item_id`, `item_name`, `item_price`, `item_interest`, `item_d_create` FROM `insert_items` WHERE 1";
$stmt = $conn->query($query);

$rowNum = 2; // Starting row for data
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $rowNum, $row['item_id']);


    $sheet->setCellValue('B' . $rowNum, $row['item_name']);
    
    
    $sheet->setCellValue('C' . $rowNum, $row['item_price'])->getStyle('C' . $rowNum)->getAlignment()->setHorizontal('center');
    
    
    $sheet->setCellValue('D' . $rowNum, $row['item_interest']);
    
    
    $sheet->setCellValue('E' . $rowNum, $row['item_d_create']);
    $rowNum++;
}

// Set the HTTP headers to prompt a file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ข้อมูลรายละเอียดรายการสินค้า.xlsx"');
header('Cache-Control: max-age=0');

// Write the spreadsheet to the output buffer
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
?>
