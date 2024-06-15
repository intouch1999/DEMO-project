<?php

// Include your database connection file here
include('../connect/connect.php');
date_default_timezone_set('Asia/Bangkok');
$decode = json_decode(file_get_contents('php://input'), true);

// Function to resize image using GD library
function resize_image($file, $w, $h, $output_path) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($width > $height) {
        $newwidth = $w;
        $newheight = $w / $r;
    } else {
        $newheight = $h;
        $newwidth = $h * $r;
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    imagejpeg($dst, $output_path, 100);
    imagedestroy($src);
    imagedestroy($dst);
}

// Process POST request
if (@$_POST['case'] == "insert") {
    try {
        $data = array('status' => 0);
        $item_name = $_POST['item_name'];
        $item_price = $_POST['item_price'];
        $item_interest = $_POST['item_interest'];
        $item_d_interest = $_POST['item_interest_date'];
        $item_type = $_POST['item_group'];
        $item_type_name = $_POST['item_group_name'];
        $item_d_create = date('Y-m-d H:i:s');

        $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING(item_id, 3) AS SIGNED)) AS max_id FROM insert_items WHERE item_id LIKE 'j-%'");
        $max_id = $stmt->fetch(PDO::FETCH_ASSOC)['max_id'];
        $last_number = ($max_id !== null) ? $max_id : 0;
        $new_id = 'j-' . str_pad($last_number + 1, 2, '0', STR_PAD_LEFT);

        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            $image = $_FILES['img']['tmp_name'];
            $image_name = $new_id . '.jpg';
            $image_path = '../src/assets/images/product/' . $image_name;

            // Resize image
            resize_image($image, 800, 600, $image_path);

            // Prepare LINE Notify API request
            $url = 'https://notify-api.line.me/api/notify';
            $token = 'Line_Token';
            // $token = 'Line_Token';
            $headers = array(
                'Authorization: Bearer ' . $token,
            );

            // Prepare image data for attachment
            $image_data = file_get_contents($image_path);

            $formate_price = number_format($item_price, 2, '.', ',');

            // Set multipart form data
            $post_data = array(
                'message' => "\n รหัสสินค้า: $new_id \n ชื่อสินค้า: $item_name \n ราคา: $formate_price บาท
                \n ประเภทสินค้า: $item_type_name  \n ดอกเบี้ย: $item_interest % \n ครบชำระดอกเบี้ย: $item_d_interest วัน
                \n วันที่สร้าง: $item_d_create \n",
                'imageFile' => new CURLFile($image_path, 'image/jpeg', $image_name)
            );

            // Send request to LINE Notify API using cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // Insert into database
            $query = "INSERT INTO insert_items (item_id, item_name, item_price, item_interest, item_d_interest, item_type, item_pic, item_d_create) 
                      VALUES ('$new_id', '$item_name', '$item_price', '$item_interest', '$item_d_interest', '$item_type_name', '$image_name', '$item_d_create')";
            $stmt = $conn->query($query);

            if ($stmt) {
                $data = array('status' => 1);
            } else {
                $data = array('status' => 0);
            }
        } else {
            $data['error_message'] = 'Error uploading image.';
        }
    } catch (PDOException $e) {
        $data['error_message'] = $e->getMessage();
    }

    echo json_encode($data);
}
?>
