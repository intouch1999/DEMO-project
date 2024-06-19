<?php
require_once('vendor/autoload.php');

// Database connection settings
$host = 'localhost';
$db = 'jumnum_db';
$user = 'Username_sivanat';
$pass = 'sivanatdev';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if (isset($_GET['item_id'])) {
    $item_id = $_GET['item_id'];

    // Fetch data based on item_id
    $stmt = $pdo->prepare('SELECT * FROM insert_items WHERE item_id = ?');
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();

    if ($item) {
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

        // $html = '
        // <style>
        //     body {
        //         font-family: "THSarabunNew", sans-serif;
        //         color: #333333;
        //     }
        //     h1 {
        //         text-align: center;
        //         color: #0066cc;
        //     }
        //     p {
        //         text-align: justify;
        //         line-height: 1.5;
        //         margin-bottom: 10px;
        //     }
        // </style>
        // <h1>รายละเอียดสินค้า</h1>
        // <p>รหัสสินค้า: ' . htmlspecialchars($item['item_id']) . '</p>
        // <p>ชื่อสินค้า: ' . htmlspecialchars($item['item_name']) . '</p>
        // <p>ประเภทสินค้า: ' . htmlspecialchars($item['item_type']) . '</p>
        // <p>ราคา: ' . htmlspecialchars($item['item_price']) . ' บาท</p>
        // <p>วันที่สร้าง: ' . htmlspecialchars($item['item_d_create']) . '</p>
        // ';

        $html = '
        <style>
            .header {
                text-align: center;
                color: blue; /* Example additional style */
                font-size: 24px; /* Example additional style */
            }
        </style>
        <div class="container"> 
            <h1 class="header">รายละเอียดสัญญา</h1>
        </div>
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
        $pdf->Output('item_'.$item_id.'.pdf', 'I');
        $pdf->Close();
    } else {
        echo 'Item not found.';
    }
} else {
    echo 'No item ID provided.';
}
?>
