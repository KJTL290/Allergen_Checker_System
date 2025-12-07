<?php
session_start();
include "db.php";

// Only staff can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: login.php");
    exit;
}

// Receive order from orders table
if(isset($_GET['receive_id'])){
    $receive_id = intval($_GET['receive_id']);

    // Get order data
    $stmt = $conn->prepare("SELECT user_id, food_id FROM orders WHERE id = ?");
    $stmt->bind_param("i", $receive_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
    
    if($order){
        $user_id = $order['user_id'];
        $food_id = $order['food_id'];

        // Move to received_orders with Accepted status
        $stmt = $conn->prepare("INSERT INTO received_orders (user_id, food_id, status, payment_status, created_at) VALUES (?, ?, 'Accepted', 'Pending', NOW())");
        $stmt->bind_param("is", $user_id, $food_id);
        $stmt->execute();
        $stmt->close();

        // Delete from orders table
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $receive_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: update_order.php");
    exit;
}

// Complete order (mark payment complete)
if(isset($_GET['complete_id'])){
    $complete_id = intval($_GET['complete_id']);
    $stmt = $conn->prepare("UPDATE received_orders SET status = 'Completed', payment_status = 'Paid' WHERE id = ?");
    $stmt->bind_param("i", $complete_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: update_order.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Payment & Completion - Staff</title>
<link rel="stylesheet" href="styles.css">
<style>
.payment-container { max-width: 1000px; margin: 20px auto; padding: 20px; }
.back-link { display: inline-block; margin-bottom: 20px; color: #3A8DFF; text-decoration: none; font-weight: bold; }
.back-link:hover { text-decoration: underline; }
.order-card { border: 2px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 8px; background: #fffbf0; }
.order-header { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 10px; }
.order-info { margin: 8px 0; }
.order-info strong { display: inline-block; width: 120px; }
.status-badge { display: inline-block; padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
.status-accepted { background: #fff3cd; color: #856404; }
.action-btn { padding: 10px 20px; margin-right: 10px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
.complete-btn { background: #28a745; color: white; }
.complete-btn:hover { background: #218838; }
.no-orders { text-align: center; padding: 40px; color: #666; }
.completed-section { margin-top: 40px; padding-top: 20px; border-top: 2px solid #ddd; }
.completed-card { border: 2px solid #28a745; background: #f0fdf4; }
.status-completed { background: #d4edda; color: #155724; }
</style>
</head>
<body>

<div class="payment-container">
    <a href="staff_dashboard.php" class="back-link">&larr; Back to Dashboard</a>

    <h2>💳 Orders Awaiting Payment & Completion</h2>

    <?php
    // Only fetch orders that are not completed
    $stmt = $conn->prepare("
        SELECT r.id, r.user_id, r.food_id, r.status, r.payment_status, r.created_at, u.username, u.first_name, u.last_name
        FROM received_orders r
        JOIN users u ON r.user_id = u.id
        WHERE r.status != 'Completed'
        ORDER BY r.created_at ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        echo "<div class='no-orders'><p>No orders awaiting payment.</p></div>";
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
            
            echo "<div class='order-card'>
                    <div class='order-header'>Order #" . htmlspecialchars($row['id']) . " - $client_name</div>
                    <div class='order-info'><strong>Foods:</strong> $food_list</div>
                    <div class='order-info'><strong>Total:</strong> ₱" . number_format($total_price, 2) . "</div>
                    <div class='order-info'><strong>Status:</strong> <span class='status-badge status-accepted'>" . htmlspecialchars($row['status']) . "</span></div>
                    <div class='order-info'><strong>Payment:</strong> <span class='status-badge status-accepted'>" . htmlspecialchars($row['payment_status']) . "</span></div>
                    <div class='order-info'><strong>Time:</strong> " . htmlspecialchars($row['created_at']) . "</div>
                    <div style='margin-top: 15px;'>
                        <a href='update_order.php?complete_id=" . htmlspecialchars($row['id']) . "'>
                            <button class='action-btn complete-btn'>✓ Mark as Completed & Paid</button>
                        </a>
                    </div>
                  </div>";
        }
    }
    $stmt->close();
    ?>

    <!-- Completed Orders Section -->
    <div class="completed-section">
        <h3>✅ Completed Orders (Today)</h3>
        <?php
        $stmt = $conn->prepare("
            SELECT r.id, r.user_id, r.food_id, r.status, r.payment_status, r.created_at, u.username, u.first_name, u.last_name
            FROM received_orders r
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'Completed' AND DATE(r.created_at) = CURDATE()
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 0){
            echo "<p style='color: #666;'>No completed orders today.</p>";
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
                
                echo "<div class='order-card completed-card'>
                        <div class='order-header'>Order #" . htmlspecialchars($row['id']) . " - $client_name</div>
                        <div class='order-info'><strong>Foods:</strong> $food_list</div>
                        <div class='order-info'><strong>Total:</strong> ₱" . number_format($total_price, 2) . "</div>
                        <div class='order-info'><strong>Status:</strong> <span class='status-badge status-completed'>" . htmlspecialchars($row['status']) . "</span></div>
                        <div class='order-info'><strong>Payment:</strong> <span class='status-badge status-completed'>" . htmlspecialchars($row['payment_status']) . "</span></div>
                      </div>";
            }
        }
        $stmt->close();
        ?>
    </div>
</div>

</body>
</html>
