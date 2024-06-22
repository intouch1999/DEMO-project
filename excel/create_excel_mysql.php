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
$sheet->setCellValue('B1', 'ชื่อสินค้า');
$sheet->setCellValue('C1', 'ประเภทสินค้า');
$sheet->setCellValue('D1', 'ราคาสินค้า');
$sheet->setCellValue('E1', 'ดอกเบี้ย');
$sheet->setCellValue('F1', 'ราคารวมดอก');
$sheet->setCellValue('G1', 'วันครบกำหนดชำระ');
$sheet->setCellValue('H1', 'วันที่สร้างรายการ');

// Center align column headers
$sheet->getStyle('A:E')->getAlignment()->setHorizontal('center');

// Fetch data from the database and add it to the spreadsheet
$query = "SELECT `item_id`, `item_name`, `item_price`, `item_interest`, `item_d_create` FROM `insert_items` WHERE 1";
$stmt = $conn->query($query);

function interest($price, $interest) {
    return $price * $interest / 100;
}

$rowNum = 2; // Starting row for data
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $rowNum, $row['item_id']);
    $sheet->setCellValue('B' . $rowNum, $row['item_name']);

    $sheet->setCellValue('D' . $rowNum, $row['item_price']);
    $sheet->setCellValue('E' . $rowNum, $row['item_interest']);
    $sheet->setCellValue('F' . $rowNum, $row['item_price'] + interest($row['item_price'], $row['item_interest']));

    $sheet->setCellValue('H' . $rowNum, $row['item_d_create']);
    
    $rowNum++;
}

// Set column autosize
foreach (range('A', 'E') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
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
