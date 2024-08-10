<?php

include('../connect/connect.php');
date_default_timezone_set('Asia/Bangkok');
$decode = json_decode(file_get_contents('php://input'), true);
$data_response = [];

if (@$_REQUEST['case'] == "monthly_data") {
    try {  
    $month_select = $decode['month_select']; 
        $query = " 
                WITH RECURSIVE DateSequence AS (
                    SELECT 
                        DATE('".$month_select."-01') AS date
                    UNION ALL
                    SELECT 
                        DATE_ADD(date, INTERVAL 1 DAY)
                    FROM 
                        DateSequence
                    WHERE 
                        DATE_ADD(date, INTERVAL 1 DAY) < DATE_ADD(DATE('".$month_select."-01'), INTERVAL 1 MONTH)
                )
                SELECT ds.date AS day, COUNT(i.item_id) AS total_product FROM DateSequence ds
                LEFT JOIN   insert_items i ON DATE_FORMAT(i.item_d_create, '%Y-%m-%d') = ds.date
                GROUP BY ds.date
                ORDER BY ds.date
        ";
        $stmt = $conn->query($query); 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chart_daily[] = $row;
        } 

        $query = "
        WITH RECURSIVE hours(hour) AS (
            SELECT 0
            UNION ALL
            SELECT hour + 1 FROM hours WHERE hour < 23
        )
        SELECT 
            CASE 
                WHEN h.hour = 23 THEN '23:00-00:00'
                ELSE CONCAT(
                    LPAD(h.hour, 2, '0'), ':00-',
                    LPAD(h.hour + 1, 2, '0'), ':00'
                )
            END AS time_range,
            COUNT(t.item_d_create) AS total_count
        FROM 
            hours h
        LEFT JOIN 
            insert_items t ON HOUR(t.item_d_create) = h.hour 
        AND DATE_FORMAT(t.item_d_create, '%Y-%m') = '".$month_select."'
        GROUP BY 
            h.hour
        ORDER BY 
            h.hour;
                ";
        $stmt = $conn->query($query); 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chart_hour[] = $row;
        }

        $query = "
            WITH ItemCountPerDay AS (
                SELECT 
                    DATE_FORMAT(item_d_create, '%Y-%m-%d') as day, 
                    COUNT(item_id) as daily_quantity
                FROM 
                    insert_items 
                WHERE 
                    DATE_FORMAT(item_d_create, '%Y-%m') = '".$month_select."'
                GROUP BY 
                    day
            )
            SELECT 
                day, 
                daily_quantity 
            FROM 
                ItemCountPerDay 
            ORDER BY 
                daily_quantity DESC 
            LIMIT 1;
        ";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count_max[] = $row;
        }
        
        $query = "
            WITH HourlyCount AS (
                SELECT 
                    CASE 
                        WHEN HOUR(item_d_create) = 23 THEN '23:00-00:00'
                        ELSE CONCAT(
                            LPAD(HOUR(item_d_create), 2, '0'), ':00-', 
                            LPAD(HOUR(item_d_create) + 1, 2, '0'), ':00'
                        ) 
                    END AS hour_range,
                    COUNT(item_id) AS total_count
                FROM 
                    insert_items 
                WHERE 
                    DATE_FORMAT(item_d_create, '%Y-%m') = '".$month_select."'
                GROUP BY 
                    HOUR(item_d_create)
            )
            SELECT 
                hour_range, 
                total_count
            FROM 
                HourlyCount 
            ORDER BY 
                total_count DESC 
            LIMIT 1;
        ";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count_max_hour[] = $row;
        }

        
        $query =  "SELECT COUNT(*) as item_month FROM `insert_items` WHERE DATE_FORMAT(item_d_create, '%Y-%m') = '".$month_select."'";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item_month[] = $row;
        }

        $query = " SELECT SUM(item_price) as price_month FROM `insert_items` WHERE DATE_FORMAT(item_d_create, '%Y-%m') = '".$month_select."' ";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $price_month[] = number_format($row['price_month']);
        }
        
        $query = " SELECT item_type , COUNT(*) AS total , SUM(item_price) as top5_price_month FROM `insert_items` WHERE DATE_FORMAT(item_d_create, '%Y-%m') = '".$month_select."' GROUP BY item_type ORDER BY total DESC LIMIT 5;
        ";
        $stmt = $conn->query($query); 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['top5_price_month'] = number_format($row['top5_price_month']);
            $catagory_product_month[] = $row;
        }
        
        $data_response['chart_daily'] = $chart_daily;
        $data_response['chart_hour'] = $chart_hour;
        $data_response['item_month'] = $item_month;
        $data_response['price_month'] = $price_month;
        $data_response['catagory_product_month'] = $catagory_product_month;
        $data_response['count_max'] = $count_max;
        $data_response['count_max_hour'] = $count_max_hour;
        $data_response['status'] ="success"; 
    } catch (PDOException $e) {
        $data_response = ['error_message' => $e->getMessage()];
    }

    echo json_encode($data_response);


} elseif (@$_REQUEST['case'] == "daily_data") {
    try {  
        $daily_select = $decode['daily_select']; 
        $query = " 
                WITH RECURSIVE hours(hour) AS (
                    SELECT 0
                    UNION ALL
                    SELECT hour + 1
                    FROM hours
                    WHERE hour < 23
                )
                SELECT 
                    CASE 
                        WHEN hours.hour = 23 THEN '23:00-00:00'
                        ELSE CONCAT(LPAD(hours.hour, 2, '0'), ':00-', LPAD(hours.hour + 1, 2, '0'), ':00') 
                    END AS time_range,
                    COUNT(t.item_d_create) AS total_count
                FROM 
                    hours
                LEFT JOIN 
                    insert_items t 
                ON 
                    HOUR(t.item_d_create) = hours.hour 
                    AND DATE(t.item_d_create) = '".$daily_select."'
                GROUP BY 
                    hours.hour
                ORDER BY 
                    hours.hour;
                    
        ";
        $stmt = $conn->query($query); 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chart_daily[] = $row;
        } 
        
        $query = "
            WITH HourlyCount AS (
                SELECT 
                    CASE 
                        WHEN HOUR(item_d_create) = 23 THEN '23:00-00:00'
                        ELSE CONCAT(LPAD(HOUR(item_d_create), 2, '0'), ':00-', LPAD(HOUR(item_d_create) + 1, 2, '0'), ':00')
                    END AS hour_range,
                    COUNT(item_id) AS total_count
                FROM insert_items 
                WHERE DATE(item_d_create) = '".$daily_select."'
                GROUP BY HOUR(item_d_create)
            )
            SELECT hour_range, total_count
            FROM HourlyCount 
            ORDER BY total_count DESC 
            LIMIT 1;
        ";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count_max[] = $row;
        }

        // Count item for the selected day
        $query = " SELECT COUNT(*) as item_today FROM `insert_items` WHERE DATE(item_d_create) = '".$daily_select."'";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item_today[] = $row;
        }

        // Sum item for the selected day
        $query = " SELECT SUM(item_price) as today_price FROM `insert_items` WHERE DATE(item_d_create) = '".$daily_select."'";
        $stmt = $conn->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $today_price[] = number_format($row['today_price']);
        }
        
        // Count top 5 categories for the selected day
        $query = " SELECT item_type, COUNT(*) AS total, SUM(item_price) as today_top5_price 
                   FROM `insert_items` 
                   WHERE DATE(item_d_create) = '".$daily_select."'
                   GROUP BY item_type 
                   ORDER BY total DESC 
                   LIMIT 5";
        $stmt = $conn->query($query); 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['today_top5_price'] = number_format($row['today_top5_price']);
            $today_catagory_product[] = $row;
        }
        
        $data_response['chart_daily'] = $chart_daily;
        $data_response['count_max'] = $count_max;
        $data_response['item_today'] = $item_today;
        $data_response['today_price'] = $today_price;
        $data_response['today_catagory_product'] = $today_catagory_product;
        $data_response['status'] = "success"; 
    } catch (PDOException $e) {
        $data_response = ['error_message' => $e->getMessage()];
    }

    echo json_encode($data_response);
}

?>
