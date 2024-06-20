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

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT); // Set top margin to 0

        // Set header margin (if using header)
        $pdf->SetHeaderMargin(0);
        // Add a page
        $pdf->AddPage();

        // Define font constants
        define('THSarabunNew', TCPDF_FONTS::addTTFfont('vendor/tecnickcom/tcpdf/fonts/THSarabunNew.ttf', 'TrueTypeUnicode'));
        define('THSarabunNew_Bold', TCPDF_FONTS::addTTFfont('vendor/tecnickcom/tcpdf/fonts/THSarabunNew Bold.ttf', 'TrueTypeUnicode'));

        // Set font
        $pdf->SetFont(THSarabunNew, '', 14);

        // Header
        $pdf->SetFont(THSarabunNew, 'B', 20);
        $pdf->Cell(0, 0, 'สัญญาขายฝาก', 0, 1, 'C');
        $pdf->Ln(10);

        // Row with left and right aligned text
        $pdf->SetFont(THSarabunNew, '', 14);
        $pdf->SetX(30);
        $pdf->Cell(0, 0, 'เลขที่สัญญา ' . $item_id, 0, 0, 'L');
        $pdf->SetX(120);
        $pdf->Cell(0, 0, 'ทำที่สำนักงานใหญ่', 0, 1, 'J');
        $pdf->Ln(5);

        function thaiMonth($month) {
            switch ($month) {
                case 'January':
                    return 'มกราคม';
                case 'February':
                    return 'กุมภาพันธ์';
                case 'March':
                    return 'มีนาคม';
                case 'April':
                    return 'เมษายน';
                case 'May':
                    return 'พฤษภาคม';
                case 'June':
                    return 'มิถุนายน';
                case 'July':
                    return 'กรกฎาคม';
                case 'August':
                    return 'สิงหาคม';
                case 'September':
                    return 'กันยายน';
                case 'October':
                    return 'ตุลาคม';
                case 'November':
                    return 'พฤศจิกายน';
                case 'December':
                    return 'ธันวาคม';
                default:
                    return $month; // ถ้าไม่มีใน case ให้คืนค่าเดิม
            }
        }
        

        $pdf->SetFont(THSarabunNew, '', 14);
        $pdf->SetX(100);
        $month = date('F', strtotime($item['item_d_create']));
        $thai_month = thaiMonth($month);

        $date_thai = 'วันที่ ' . date('d', strtotime($item['item_d_create'])) . ' ' . $thai_month . ' ' . (date('Y', strtotime($item['item_d_create'])) + 543);

        $pdf->Cell(0, 0, $date_thai, 0, 0, 'L');
        $pdf->Ln(10);

        $pdf->SetFont(THSarabunNew, '', 14);
        $pdf->MultiCell(0, 0, "สัญญานี้ทำขึ้นระหว่าง นายกนกศักดิ์ นากประทุม อายุ 35 ปี อยู่บ้านเลขที่ 45/121 หมู่ 5 ซอยเสรีไทย 32 ถนนเสรีไทย\nแขวงคันนายาว เขตคันนายาว กรุงเทพฯ ซึ่งต่อไปในสัญญานี้เรียกว่าผู้ขายฝาก ฝ่ายหนึ่ง\nกับ คุณโคมล อยู่บ้ายเลขที่ AAA BBB ซึ่งต่อไปในสัญญานี้เรียกว่าผู้ซื้อฝาก อีกฝ่ายหนึ่ง\nทั้งสองฝ้ายตกลงทำสัญญากันมีข้อความดังต่อไปนี้ คือ", 0, 'J');



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
            h1 {
                font-size: 24px;
            }
            body {
                font-family: "THSarabunNew", sans-serif;
                margin: 50px;
                line-height: 1.6;
            }
            .center {
                text-align: center;
            }
            .underline {
                text-decoration: underline;
            }
            .bold {
                font-weight: bold;
            }
            .indent {
                text-indent: 2em;
            }
            .highlight {
                background-color: yellow;
            }
            .signature {
                margin-top: 50px;
            }
            .signature span {
                display: inline-block;
                width: 200px;
                border-bottom: 1px solid black;
                margin: 0 10px;
            }
        </style>
        <p>สัญญานี้ทำขึ้นระหว่าง นายกนกศักดิ์ นากประทุม อายุ 35 ปี อยู่บ้านเลขที่ 45/121 หมู่ 5 ซอยเสรีไทย 32 ถนนเสรีไทย\nแขวงคันนายาว เขตคันนายาว กรุงเทพฯ ซึ่งต่อไปในสัญญานี้เรียกว่าผู้ขายฝาก ฝ่ายหนึ่ง\nกับ คุณโคมล อยู่บ้ายเลขที่ AAA BBB ซึ่งต่อไปในสัญญานี้เรียกว่าผู้ซื้อฝาก อีกฝ่ายหนึ่ง\nทั้งสองฝ้ายตกลงทำสัญญากันมีข้อความดังต่อไปนี้ คือ</p>
        <ol>
            <li class="indent">
                ผู้ขายฝากตกลงขายฝากและผู้รับซื้อฝากตกลงรับซื้อฝาก โฉนดที่ดิน เลขที่ <span class="highlight">12345</span> เลขที่ดิน <span class="highlight">67890</span>
                หน้าสำรวจ <span class="highlight">1234</span> ซึ่งเป็นกรรมสิทธิ์ของผู้ขายฝากเป็นจำนวนเงินทั้งสิ้น <span class="highlight">2,000,000 บาท (สองล้านบาทถ้วน)</span>
            </li>
            <li class="indent">
                ในระหว่างที่มีสัญญานี้ผู้ขายฝากมีสิทธิไถ่คืนตลอดเวลา ผู้รับซื้อฝากจะไม่คิดดอกเบี้ย ค่าไถ่ถอน หรือบังคับผู้ขายฝากให้นำเงินมาใช้คืน
            </li>
            <li class="indent">
                ผู้ขายฝากตกลงไถ่คืนภายในกำหนดระยะเวลาไถ่ถอนในวันที่ <span class="highlight">3 มกราคม 2552</span> โดยตกลงกันเป็นจำนวน <span class="highlight">2,400,000 บาท (สองล้านสี่แสนบาทถ้วน)</span>
            </li>
        </ol>
        <p class="indent">
            จึงทำสัญญานี้ไว้เป็นพยานในการตกลงและต่างฝ่ายต่างได้อ่านให้ทราบข้อความในสัญญาฉบับนี้จนเป็นที่เข้าใจดีแล้ว จึงได้ลงลายมือชื่อไว้เป็นสำคัญต่อหน้าพยาน
        </p>
        <div class="signature center">
            <p>ลงชื่อ ___________________ ผู้ขายฝาก</p>
            <p>(นางเกตุกนก แก้วพฤกษ์)</p>
            <p>ลงชื่อ ___________________ ผู้รับซื้อฝาก</p>
            <p>(สุดเดช สมบัติไพบูลย์)</p>
            <p>ลงชื่อ ___________________ พยาน</p>
            <p>ลงชื่อ ___________________ พยาน</p>
        </div>
        ';

        $pdf->writeHTML($html, true, false, true, false, '');

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
