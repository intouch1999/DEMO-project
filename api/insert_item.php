<?php
include('../connect/connect.php');
date_default_timezone_set('Asia/Bangkok');
$decode = json_decode(file_get_contents('php://input'), true);

if(@$_POST['case'] == "insert") {
    try {
        $data = array('status' => 0);
        $item_name = $_POST['item_name'];
        $item_price = $_POST['item_price'];
        $item_interest = $_POST['item_interest'];
        $item_d_interest = $_POST['item_interest_date'];
        $item_type = $_POST['item_group'];
        $item_d_create = date('Y-m-d H:i:s');
        
        $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING(item_id, 3) AS SIGNED)) AS max_id FROM insert_items WHERE item_id LIKE 'j-%'");
        $max_id = $stmt->fetch(PDO::FETCH_ASSOC)['max_id'];

        $last_number = ($max_id !== null) ? $max_id : 0;

        $new_id = 'j-' . str_pad($last_number + 1, 2, '0', STR_PAD_LEFT);

        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            $image = $_FILES['img']['tmp_name'];
            $image_name = $new_id . '.jpg';
            $image_path = '../src/assets/images/product/' . $image_name;
            move_uploaded_file($image, $image_path);
            $query = "INSERT INTO insert_items (item_id, item_name, item_price, item_interest, item_d_interest, item_type, item_pic, item_d_create) 
            VALUES ('$new_id', '$item_name', '$item_price', '$item_interest', '$item_d_interest', '$item_type', '$image_name', '$item_d_create')";
            $stmt = $conn->query($query);

            if ($stmt) {
                $data = array('status' => 1);
            } else {
                $data = array('status' => 0);
            }
        } else {
            $data['error_message'] = 'Error uploading image.';
        }
    } catch(PDOException $e) {
        $data['error_message'] = $e->getMessage();
    }

    echo json_encode(@$data);
}
?>