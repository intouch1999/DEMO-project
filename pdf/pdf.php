<?php
require_once('vendor/autoload.php');

// Create new TCPDF object
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example with THSarabunNew Fonts');
$pdf->SetSubject('TCPDF Font Example');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT); // ตั้งค่า margin บนเป็น 0

// Set header margin (if using header)
$pdf->SetHeaderMargin(0);
// Add a page
$pdf->AddPage();

// Define font constants
define('THSarabunNew', TCPDF_FONTS::addTTFfont('vendor/tecnickcom/tcpdf/fonts/THSarabunNew.ttf', 'TrueTypeUnicode'));
define('THSarabunNew_Bold', TCPDF_FONTS::addTTFfont('vendor/tecnickcom/tcpdf/fonts/THSarabunNew Bold.ttf', 'TrueTypeUnicode'));

// Set font
$pdf->SetFont(THSarabunNew, '', 14);

// Write content with THSarabunNew font
$html = '
<style>
    body {
        font-family: "THSarabunNew", sans-serif;
        color: #333333;
    }
    h1 {
        text-align: center;
        color: #0066cc;
    }
    p {
        text-align: justify;
        line-height: 1.5;
        margin-bottom: 10px;
    }
</style>
<h1>สร้าง PDF ที่มีเนื้อหาและ CSS ทั้งหมด</h1>
<p>นี่คือเนื้อหาของ PDF ที่ใช้ฟอนต์ THSarabunNew และมีการใช้ CSS ทั้งหมดเพื่อจัดรูปแบบ</p>
<p>สามารถใส่ข้อความภาษาไทยหรือภาษาอังกฤษลงไปได้ตามต้องการ</p>
<p>นี่คือตัวอย่างข้อความอื่น ๆ ที่จะแสดงในหน้า PDF</p>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Set bold font
$pdf->SetFont(THSarabunNew_Bold, '', 14);

// Write more content with THSarabunNew Bold font
$html_bold = '
<p>ข้อความตัวอักษรหนาในฟอนต์ THSarabunNew Bold</p>
<p>สามารถปรับแต่งรูปแบบข้อความได้ตามความต้องการ</p>
';

$pdf->writeHTML($html_bold, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('hello-tcpdf-fonts.pdf', 'I');
$pdf->Close();
?>
