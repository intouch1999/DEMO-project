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
$sheet->getStyle('A:H')->getAlignment()->setHorizontal('center');

// Fetch data from the database and add it to the spreadsheet
$query = "SELECT `item_id`, `item_name`, `item_type`, `item_price`, `item_interest`, `item_d_interest`, `item_d_create` FROM `insert_items` WHERE 1";
$stmt = $conn->query($query);

function interest($price, $interest) {
    return $price * $interest / 100;
}

$rowNum = 2; // Starting row for data
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $rowNum, $row['item_id']);
    $sheet->setCellValue('B' . $rowNum, $row['item_name']);
    $sheet->setCellValue('C' . $rowNum, $row['item_type']);
    // $sheet->setCellValue('D' . $rowNum, $row['item_price'])->getStyle('D' . $rowNum)->getNumberFormat()->setFormatCode('฿#,##0.00;-฿#,##0.00');
    $sheet->setCellValue('D' . $rowNum, $row['item_price'])->getStyle('D' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->setCellValue('E' . $rowNum, "0.".$row['item_interest'])->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('0.0%');
    $sheet->setCellValue('F' . $rowNum, $row['item_price'] + interest($row['item_price'], $row['item_interest']));
    $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
    
    // คำนวณวันครบกำหนดชำระ โดยเริ่มนับจากวันที่สร้างรายการและเพิ่มจำนวนวันจาก item_d_interest
    $createDate = new DateTime($row['item_d_create']);
    $interestDays = (int) $row['item_d_interest'];
    $createDate->modify("+{$interestDays} days"); // เพิ่มจำนวนวันที่กำหนดใน item_d_interest
    $dueDate = $createDate->format('d/m/Y');
    
    $sheet->setCellValue('G' . $rowNum, $dueDate)->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('dd/mm/yyyy'); // ตั้งค่าวันที่ครบกำหนดชำระ
    
    $DateTime = new DateTime($row['item_d_create']);
    $NDT = $DateTime->format('d/m/Y H:i');

    $sheet->setCellValue('H' . $rowNum, $NDT)->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('d/m/yyyy h:mm'); // ตั้งค่าวันที่สร้างรายการ
    
    $rowNum++;
}

// Set column autosize
foreach (range('A', 'H') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Set the HTTP headers to prompt a file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ข้อมูลรายละเอียดรายสินค้าทั้งหมด.xlsx"');
header('Cache-Control: max-age=0');

// Write the spreadsheet to the output buffer
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
?>
