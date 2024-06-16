<?php

include('../connect/connect.php');
date_default_timezone_set('Asia/Bangkok');
$decode = json_decode(file_get_contents('php://input'), true);

if (@$decode['case'] == "items") {
    try {
        $itemsPerPage = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $itemsPerPage;

        $query = "SELECT * FROM `insert_items` ORDER BY item_id DESC LIMIT $itemsPerPage OFFSET $offset";
        $stmt = $conn->query($query);
        
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
        } else {
            $data = [];
        }
        $data[0]['status'] = 1;
    } catch (PDOException $e) {
        $data['error_message'] = $e->getMessage();
    }

    if (!isset($data)) {
        $data = ['status' => 0]; 
    }

    echo json_encode($data);

} else if (@$decode['case'] == "DayCount") {
    try {
        $data = array('status' => 0);

        // ใช้ PDO ในการเตรียมและดำเนินการคำสั่ง SQL
        $sql = "SELECT COUNT(*) as item_d_create FROM `insert_items` WHERE DATE(item_d_create) = CURDATE()"; 
        $stmt = $conn->query($sql);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $data = array('status' => 1, 'todaycount' => $row['item_d_create']);
        } else {
            $data['status'] = 0;
        }
    } catch (PDOException $e) {
        $data['error_message'] = $e->getMessage();
    }

    echo json_encode($data);

} else if (@$decode['case'] == "AllCount") {
    try {
        $data = array('status' => 0);

        // ใช้ PDO ในการเตรียมและดำเนินการคำสั่ง SQL
        $sql = "SELECT COUNT(*) as item_d_create FROM `insert_items`"; 
        $stmt = $conn->query($sql);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $data = array('status' => 1, 'allcount' => $row['item_d_create']);
        } else {
            $data['status'] = 0;
        }
    } catch (PDOException $e) {
        $data['error_message'] = $e->getMessage();
    }

    echo json_encode($data);

}
?>
