<?php

// Include your database connection file here
include('../connect/connect.php');
date_default_timezone_set('Asia/Bangkok');
$decode = json_decode(file_get_contents('php://input'), true);

// Function to resize image using GD library
function resize_image($file, $w, $h, $output_path) {
    $image_info = getimagesize($file);
    $mime_type = $image_info['mime']; // Get MIME type of the image

    // Determine image type and create image resource accordingly
    switch ($mime_type) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($file);
            break;
        case 'image/png':
            $src = imagecreatefrompng($file);
            break;
        default:
            throw new Exception('Unsupported image type.');
    }

    $width = imagesx($src);
    $height = imagesy($src);
    $r = $width / $height;

    if ($width > $height) {
        $newwidth = $w;
        $newheight = $w / $r;
    } else {
        $newheight = $h;
        $newwidth = $h * $r;
    }

    $dst = imagecreatetruecolor($newwidth, $newheight);

    // Resize the image
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    // Output the resized image to a file
    switch ($mime_type) {
        case 'image/jpeg':
            imagejpeg($dst, $output_path, 100);
            break;
        case 'image/png':
            imagepng($dst, $output_path, 9); // Use compression level 9 for PNG
            break;
    }

    // Clean up resources
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

        $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING(item_id, 7) AS SIGNED)) AS max_id FROM insert_items WHERE item_id LIKE 'T01-67%'");
        $max_id = $stmt->fetch(PDO::FETCH_ASSOC)['max_id'];
        $last_number = ($max_id !== null) ? $max_id : 0;
        
        $new_id_prefix = 'T01-67';
        $number_length = 5;
        
        // Extract the numeric part and increment it
        $last_number++;
        
        // Pad the number with leading zeros
        $new_id_number = str_pad($last_number, $number_length, '0', STR_PAD_LEFT);
        
        // Concatenate everything together to form the new ID
        $new_id = $new_id_prefix . $new_id_number;

        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            $image = $_FILES['img']['tmp_name'];
            $image_name = $new_id . '.jpg';
            $image_path = '../src/assets/images/product/' . $image_name;

            // Resize image
            resize_image($image, 800, 600, $image_path);

            // Prepare LINE Notify API request
            $url = 'https://notify-api.line.me/api/notify';
            // ส่วนตัว
            //$token = 'Line_Token';
            // old group
            //  $token = 'Line_Token';
             //new group
             $token = 'Line_Token';
            $headers = array(
                'Authorization: Bearer ' . $token,
            );

            // Prepare image data for attachment
            $image_data = file_get_contents($image_path);

            function price_interest($item_price, $item_interest) {
                $interest = $item_price * $item_interest / 100;
                return $interest;
            }

            function datetime($item_d_interest, $item_d_create) {
                $createDate = new DateTime($item_d_create);
                $createDate->modify("+{$item_d_interest} days");
                return $createDate->format('d/m/Y');
            }

            // Set multipart form data
            $post_data = array(
                'message' => "\n รหัสสินค้า: $new_id \n ชื่อสินค้า: $item_name \n ประเภทสินค้า: $item_type_name \n ราคาฝาก: ".number_format($item_price, 2, '.', ',')." บาท \n ดอกเบี้ย(%): $item_interest % \n ดอกเบี้ย: " . number_format(price_interest($item_price, $item_interest) , 2, '.', ',') . " บาท \n ราคาไถ่ถอน: " . number_format($item_price + price_interest($item_price, $item_interest), 2, '.', ',') . " บาท\n ครบชำระดอกเบี้ย: " . datetime($item_d_interest, $item_d_create) . "
                ",
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
