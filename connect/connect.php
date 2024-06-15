<?php
$servername = "localhost";
$username = "Username_sivanat";
$password = "sivanatdev";
$dbname = "jumnum_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // เซ็ตให้ PDO เป็นโหมดแจ้งเตือนข้อผิดพลาด
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
