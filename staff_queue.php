<?php
session_start();
include "db.php";

// Only staff can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Queue - Staff Dashboard</title>
<link rel="stylesheet" href="styles.css">
<style>
.queue-container { max-width: 1000px; margin: 20px auto; padding: 20px; }
.back-link { display: inline-block; margin-bottom: 20px; color: #3A8DFF; text-decoration: none; font-weight: bold; }
.back-link:hover { text-decoration: underline; }
.order-card { border: 2px solid #3A8DFF; padding: 15px; margin: 15px 0; border-radius: 8px; background: #f0f7ff; }
.order-header { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 10px; }
.order-info { margin: 8px 0; }
.order-info strong { display: inline-block; width: 120px; }
.action-btn { padding: 10px 20px; margin-right: 10px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
.receive-btn { background: #28a745; color: white; }
.receive-btn:hover { background: #218838; }
.allergen-info { background: #fff3cd; padding: 8px 12px; border-radius: 5px; margin-top: 10px; border-left: 4px solid #ffc107; }
.no-orders { text-align: center; padding: 40px; color: #666; }
</style>
</head>
<body>

<div class="queue-container">
    <a href="staff_dashboard.php" class="back-link">&larr; Back to Dashboard</a>

    <h2>📋 Pending Order Queue</h2>

    <?php
    $stmt = $conn->prepare("
        SELECT o.id, o.user_id, o.food_id, o.created_at, u.username, u.first_name, u.last_name, u.allergies
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        echo "<div class='no-orders'><p>No pending orders in queue.</p></div>";
    } else {
        while($row = $result->fetch_assoc()){
            $food_ids = array_map('intval', explode(",", $row['food_id']));
            $food_names = [];
            $total_price = 0;

            foreach($food_ids as $fid){
                $food_stmt = $conn->prepare("SELECT name, price FROM food WHERE id = ?");
                $food_stmt->bind_param("i", $fid);
                $food_stmt->execute();
                $food_result = $food_stmt->get_result();
                $f = $food_result->fetch_assoc();
                $food_stmt->close();
                
                if($f){
                    $food_names[] = htmlspecialchars($f['name']);
                    $total_price += floatval($f['price']);
                }
            }

            $food_list = implode(", ", $food_names);
            $client_name = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
            $allergies = $row['allergies'] ? htmlspecialchars($row['allergies']) : "None";
            
            echo "<div class='order-card'>
                    <div class='order-header'>Order #" . htmlspecialchars($row['id']) . " - $client_name</div>
                    <div class='order-info'><strong>Foods:</strong> $food_list</div>
                    <div class='order-info'><strong>Total:</strong> ₱" . number_format($total_price, 2) . "</div>
                    <div class='order-info'><strong>Time:</strong> " . htmlspecialchars($row['created_at']) . "</div>
                    <div class='allergen-info'><strong>⚠ Client Allergies:</strong> $allergies</div>
                    <div style='margin-top: 15px;'>
                        <a href='update_order.php?receive_id=" . htmlspecialchars($row['id']) . "'>
                            <button class='action-btn receive-btn'>Receive & Accept Order</button>
                        </a>
                    </div>
                  </div>";
        }
    }
    $stmt->close();
    ?>
</div>

</body>
</html>
