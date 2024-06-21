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
        // $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT); // Set top margin to 0
        $pdf->SetMargins(10, 10, 10 );
        $pdf->setAutoPageBreak(false, PDF_MARGIN_BOTTOM);
        // Set header margin (if using header)
        $pdf->SetHeaderMargin(0);
        // Add a page
        $pdf->AddPage();
        //set space between lines

        // Define font constants
        define('THSarabunNew', TCPDF_FONTS::addTTFfont('vendor/tecnickcom/tcpdf/fonts/THSarabunNew.ttf', 'TrueTypeUnicode'));
        define('THSarabunNew_Bold', TCPDF_FONTS::addTTFfont('vendor/tecnickcom/tcpdf/fonts/THSarabunNew Bold.ttf', 'TrueTypeUnicode'));

        // Set font
        $pdf->SetFont(THSarabunNew, '', 16);

        // Header
        $pdf->SetFont(THSarabunNew, 'B', 20);
        $pdf->Cell(0, 0, 'สัญญาขายฝาก', 0, 1, 'C');
        $pdf->Ln(10);

        // Row with left and right aligned text
        $pdf->SetFont(THSarabunNew, '', 16);
        $pdf->SetX(11);
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
        

        $pdf->SetFont(THSarabunNew, '', 16);
        $pdf->SetX(100);
        $month = date('F', strtotime($item['item_d_create']));
        $thai_month = thaiMonth($month);

        $date_thai = 'วันที่ ' . date('d', strtotime($item['item_d_create'])) . ' ' . $thai_month . ' ' . (date('Y', strtotime($item['item_d_create'])) + 543);

        $pdf->Cell(0, 0, $date_thai, 0, 0, 'L');
        $pdf->Ln(10);

        $pdf->SetFont(THSarabunNew, '', 16);
        $pdf->SetX(11);
        $pdf->MultiCell(0, 0, "สัญญานี้ทำขึ้นระหว่าง นายกนกศักดิ์ นากประทุม อายุ 35 ปี อยู่บ้านเลขที่ 45/121 หมู่ 5 ซอยเสรีไทย 32 ถนนเสรีไทย\nแขวงคันนายาว เขตคันนายาว กรุงเทพฯ ซึ่งต่อไปในสัญญานี้เรียกว่าผู้ขายฝาก ฝ่ายหนึ่ง\nกับ คุณโคมล อยู่บ้ายเลขที่ AAA BBB ซึ่งต่อไปในสัญญานี้เรียกว่าผู้ซื้อฝาก อีกฝ่ายหนึ่ง\nทั้งสองฝ้ายตกลงทำสัญญากันมีข้อความดังต่อไปนี้ คือ", 0, 'L');
        $pdf->Ln(5);



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

        function convertNumberToThaiText($number) {
            $textNumbers = array(
                '0' => 'ศูนย์', '1' => 'หนึ่ง', '2' => 'สอง', '3' => 'สาม',
                '4' => 'สี่', '5' => 'ห้า', '6' => 'หก', '7' => 'เจ็ด',
                '8' => 'แปด', '9' => 'เก้า', '10' => 'สิบ', '100' => 'ร้อย',
                '1000' => 'พัน', '10000' => 'หมื่น', '100000' => 'แสน', '1000000' => 'ล้าน'
            );
        
            $unitNames = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
        
            $numberStr = (string)$number;
            $length = strlen($numberStr);
            $thaiText = '';
        
            for ($i = 0; $i < $length; $i++) {
                $digit = $numberStr[$i];
                $position = $length - $i - 1;
        
                if ($digit != '0') {
                    if ($position == 1 && $digit == '1') {
                        $thaiText .= 'สิบ';
                    } elseif ($position == 1 && $digit == '2') {
                        $thaiText .= 'ยี่สิบ';
                    } elseif ($position == 0 && $digit == '1' && $length > 1) {
                        $thaiText .= 'เอ็ด';
                    } else {
                        $thaiText .= $textNumbers[$digit] . $unitNames[$position];
                    }
                }
            }
        
            return $thaiText . 'บาทถ้วน';
        }

        function express_date($date, $days) {
            $datetime = new DateTime($date);
            $datetime->modify("+$days days"); // Add days to the original date
            $month = date('F', strtotime($datetime->format('Y-m-d'))); // Get month name
            $thai_month = thaiMonth($month); // Convert month name to Thai
            $thai_year = date('Y', strtotime($datetime->format('Y-m-d'))) + 543; // Convert year to Thai format
        
            $formatted_date = 'วันที่ ' . $datetime->format('j') . ' ' . $thai_month . ' ' . $thai_year;
        
            return $formatted_date;
        }
        

        $expressed_date = express_date($item['item_d_create'], $item['item_d_interest']);

        $html = '
        <style>
            h1 {
                font-size: 24px;
            }
            body {
                font-family: "THSarabunNew", sans-serif;
                color: #333333;
                margin: 0; /* Set margin to 0 for the entire body */
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
                margin: 0; /* Set margin to 0 for paragraphs with the indent class */
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
          p {
    margin-bottom: 10px; /* Adjust as needed */
}

        </style>
        <p class="indent">1) ผู้ขายฝากตกลงขายฝากและผู้ซื้อฝากกตกลกรับซื้อฝาก '.($item['item_name']).' 
        <br>ซึ่งเป็นกรรมสิทธิ์ของผู้ขายฝากเป็นจำนวนเงินทั้งสิ้น '.number_format($item['item_price']).' ('.convertNumberToThaiText(($item['item_price'])).')</p>
        <p class="indent">2) ในระว่างที่อยู่ในระยะเวลาแห่งการไถ่ทรัพย์สินที่ขายฝาก ผู้ซื้อฝากจะไม่ดำเนินการเกี่ยวกับนิติกรรมใดๆ บนทรัพย์สิน
        <br>ที่รับซื้อฝากมานั้น</p>
        <p class="indent">3) ผู้ซื้อฝากตกลงให้ผู้ขายฝากทำการไถ่ทรัพย์สินที่ขายฝากได้ภายใน '.$expressed_date.' โดยกำหนดสินไถ่ไว้เป็นเงินจำนวน
        <br>'.number_format($item['item_price']).' ('.convertNumberToThaiText(($item['item_price'])).') </p>
        <p class="indent">4) ค่าฤชาธรรมเนียมในการขายฝาก และค่าฤชาธรรมเนียมในการไถ่ทรัพย์สินที่ขายฝากนั้น ผู้ขายฝากเป็นผู้รับภาระทั้งสิ้น</p>
        <p class="indent">5) ทรัพย์สินไุถ่นั้นจะต้องส่งคืนตามสภาพที่เป้นอยู๋ ณ เวลาที่ทำการไถ่ ถ้าหากทรัพย์สินนั้นถูกทำลายหรือทำให้เสื่อมเสียไป
        <br>เพราะความผิดของผู้ซื้อฝาก ผู้ซื้อฝากต้องชดใช้ค่าสินไหมทดแทนในความเสียหายนั้นให้แก่ผู้ขายฝาก</p>
        <p class="indent">สัญญานี้ถูกทำขึ้นเป็นสองฉบับมีข้อความถูกต้องตรงกัน คู่สัญญาทั้งสองฝ่ายได้อ่านและเข้าใจโดยตลอดแล้วจึงลงลายมือชื่อ
        <br>ไว้ต่อหน้าพยานเป็นสำคัญและเก็บสัญญาไว้ฝ่ายละฉบับ
        </p>

        ';

        
       

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->LN(10);

        $pdf->SetFont(THSarabunNew, '', 16);
        $pdf->SetX(100);
        $pdf->Cell(0, 0, "ลงชื่อ ___________________ ผู้ขายฝาก", 0, 0, 'L');
        $pdf->LN(7);
        $pdf->SetX(110);
        $pdf->Cell(0, 0, '(นายกนกศักดิ์ นากประทุม)', 0, 1, 'L');
        $pdf->Ln(5);
        $pdf->SetX(100);
        $pdf->Cell(0, 0, "ลงชื่อ ___________________ ผู้รับซื้อฝาก", 0, 0, 'L');
        $pdf->LN(7);
        $pdf->SetX(120);
        $pdf->Cell(0, 0, '(คณโคมล)', 0, 1, 'L');
        $pdf->Ln(5);
        $pdf->SetX(100);
        $pdf->Cell(0, 0, "ลงชื่อ ___________________ พยาน", 0, 0, 'L');
        $pdf->Ln(7);
        $pdf->SetX(109);
        $pdf->Cell(0, 0, '(.........................................)', 0, 1, 'L');
        $pdf->Ln(5);
        $pdf->SetX(100);
        $pdf->Cell(0, 0, "ลงชื่อ ___________________ พยาน", 0, 0, 'L');
        $pdf->Ln(7);
        $pdf->SetX(109);
        $pdf->Cell(0, 0, '(.........................................)', 0, 1, 'L');
        $pdf->Ln(5);
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
