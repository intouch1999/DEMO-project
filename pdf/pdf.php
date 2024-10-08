<?php
require_once('vendor/autoload.php');

// Database connection settings
$host = 'localhost';
$db = 'DB_NAME';
$user = 'Username';
$pass = 'Password';
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
        $pdf->SetTitle('ใบสัญญาสินค้ารหัส ' . $item_id);
        $pdf->SetSubject('ใบสัญญาสินค้ารหัส ' . $item_id);
        $pdf->SetKeywords('ใบสัญญา');

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
        $pdf->Cell(0, 0, 'เลขที่สัญญา ', 0, 0, 'L');
        $pdf->SetFont(THSarabunNew, 'B', 16);
        $pdf->SetX(30);
        $pdf->Cell(0, 0, $item_id, 0, 0, 'L');
        $pdf->SetFont(THSarabunNew, '', 16);
        $pdf->SetX(120);
        $pdf->Cell(0, 0, 'ทำที่ สำนักงานใหญ่', 0, 1, 'L');
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
        

        $pdf->SetFont(THSarabunNew, 'B', 16);
        $pdf->SetX(100);
        $month = date('F', strtotime($item['item_d_create']));
        $thai_month = thaiMonth($month);

        $date_thai = 'วันที่ ' . date('d', strtotime($item['item_d_create'])) . ' ' . $thai_month . ' ' . (date('Y', strtotime($item['item_d_create'])) + 543);

        $pdf->Cell(0, 0, $date_thai, 0, 0, 'L');
        $pdf->Ln(10);

        $pdf->SetFont(THSarabunNew, '', 16);
        $pdf->SetX(9);
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

        function Convert($amount_number)
        {
            $amount_number = number_format($amount_number, 2, ".", "");
            $pt = strpos($amount_number, ".");
            $number = $fraction = "";
            if ($pt === false) 
                $number = $amount_number;
            else
            {
                $number = substr($amount_number, 0, $pt);
                $fraction = substr($amount_number, $pt + 1);
            }
            
            $ret = "";
            $baht = ReadNumber($number);
            if ($baht != "")
                $ret .= $baht . "บาท";
            
            $satang = ReadNumber($fraction);
            if ($satang != "")
                $ret .=  $satang . "สตางค์";
            else 
                $ret .= "ถ้วน";
            
            return $ret;
        }
        
        function ReadNumber($number)
        {
            $position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
            $number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
            $number = $number + 0;
            $ret = "";
            if ($number == 0) return $ret;
            if ($number > 1000000)
            {
                $ret .= ReadNumber(intval($number / 1000000)) . "ล้าน";
                $number = intval(fmod($number, 1000000));
            }
            
            $divider = 100000;
            $pos = 0;
            while($number > 0)
            {
                $d = intval($number / $divider);
                $ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : 
                    ((($divider == 10) && ($d == 1)) ? "" :
                    ((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
                $ret .= ($d ? $position_call[$pos] : "");
                $number = $number % $divider;
                $divider = $divider / 10;
                $pos++;
            }
            return $ret;
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

        function interest ($price , $rate) {
            $interest = ($price * $rate) / 100;
            return $interest;
        }

        $price_interest = $item['item_price'] + interest($item['item_price'], $item['item_interest']);

        $price_interest_formatTH = Convert($price_interest);

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
        <p>1) ผู้ขายฝากตกลงขายฝากและผู้ซื้อฝากกตกลกรับซื้อฝาก <b>'.($item['item_name']).'</b> 
        <br>ซึ่งเป็นกรรมสิทธิ์ของผู้ขายฝากเป็นจำนวนเงินทั้งสิ้น <b>'.number_format($item['item_price'] , 2).' บาท ('.Convert(($item['item_price'])).')</b></p>
        <p>2) ในระว่างที่อยู่ในระยะเวลาแห่งการไถ่ทรัพย์สินที่ขายฝาก ผู้ซื้อฝากจะไม่ดำเนินการเกี่ยวกับนิติกรรมใดๆ บนทรัพย์สิน
        <br>ที่รับซื้อฝากมานั้น</p>
        <p>3) ผู้ซื้อฝากตกลงให้ผู้ขายฝากทำการไถ่ทรัพย์สินที่ขายฝากได้ภายใน <b>'.$expressed_date.'</b> โดยกำหนดสินไถ่ไว้เป็นเงินจำนวน
        <br><b>'.number_format($price_interest , 2).' บาท ('.$price_interest_formatTH.')</b> </p>
        <p>4) ค่าฤชาธรรมเนียมในการขายฝาก และค่าฤชาธรรมเนียมในการไถ่ทรัพย์สินที่ขายฝากนั้น ผู้ขายฝากเป็นผู้รับภาระทั้งสิ้น</p>
        <p>5) ทรัพย์สินไุถ่นั้นจะต้องส่งคืนตามสภาพที่เป้นอยู๋ ณ เวลาที่ทำการไถ่ ถ้าหากทรัพย์สินนั้นถูกทำลายหรือทำให้เสื่อมเสียไป
        <br>เพราะความผิดของผู้ซื้อฝาก ผู้ซื้อฝากต้องชดใช้ค่าสินไหมทดแทนในความเสียหายนั้นให้แก่ผู้ขายฝาก</p>
        <p>สัญญานี้ถูกทำขึ้นเป็นสองฉบับมีข้อความถูกต้องตรงกัน คู่สัญญาทั้งสองฝ่ายได้อ่านและเข้าใจโดยตลอดแล้วจึงลงลายมือชื่อ
        <br>ไว้ต่อหน้าพยานเป็นสำคัญและเก็บสัญญาไว้ฝ่ายละฉบับ
        </p>

        ';

        
       

        $pdf->writeHTML($html, true, false, true, false, 'L');
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
        $pdf->Output('ใบสัญญาสินค้ารหัส '.$item_id.'.pdf', 'I');
        $pdf->Close();
    } else {
        echo 'Item not found.';
    }
} else {
    echo 'No item ID provided.';
}
?>
