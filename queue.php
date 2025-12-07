<?php 
include "db.php"; 
session_start();

if($_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Queue - Admin View</title>
<link rel="stylesheet" href="styles.css">
<style>
.queue-container { max-width: 1000px; margin: 20px auto; padding: 20px; }
.back-link { display: inline-block; margin-bottom: 20px; color: #3A8DFF; text-decoration: none; font-weight: bold; }
.back-link:hover { text-decoration: underline; }
.order-item { border: 1px solid #ddd; padding: 12px; margin: 10px 0; border-radius: 8px; background: #f9f9f9; display: flex; justify-content: space-between; align-items: center; }
.order-info { flex: 1; }
.order-status { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
.status-pending { background: #fff3cd; color: #856404; }
.status-accepted { background: #cfe2ff; color: #084298; }
.status-completed { background: #d4edda; color: #155724; }
.no-orders { text-align: center; padding: 40px; color: #666; }
</style>
</head>
<body>

<div class="queue-container">
    <a href="admin_dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    
    <h2>📋 All Orders - System Queue</h2>

    <?php
    // Pending orders
    echo "<h3>⏳ Pending Orders</h3>";
    $stmt = $conn->prepare("
        SELECT o.id, o.status, u.first_name, u.last_name
        FROM orders o 
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 0){
        echo "<p style='color: #666;'>No pending orders.</p>";
    } else {
        while($r = $result->fetch_assoc()){
            echo "<div class='order-item'>
                    <div class='order-info'>
                        Order #" . htmlspecialchars($r['id']) . " - " . htmlspecialchars($r['first_name'] . " " . $r['last_name']) . "
                    </div>
                    <span class='order-status status-pending'>" . htmlspecialchars($r['status']) . "</span>
                  </div>";
        }
    }
    $stmt->close();

    // Accepted/Received orders
    echo "<h3 style='margin-top: 30px;'>🔄 Accepted Orders (Awaiting Completion)</h3>";
    $stmt = $conn->prepare("
        SELECT r.id, r.status, u.first_name, u.last_name
        FROM received_orders r
        JOIN users u ON r.user_id = u.id
        WHERE r.status != 'Completed'
        ORDER BY r.created_at ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 0){
        echo "<p style='color: #666;'>No accepted orders.</p>";
    } else {
        while($r = $result->fetch_assoc()){
            echo "<div class='order-item'>
                    <div class='order-info'>
                        Order #" . htmlspecialchars($r['id']) . " - " . htmlspecialchars($r['first_name'] . " " . $r['last_name']) . "
                    </div>
                    <span class='order-status status-accepted'>" . htmlspecialchars($r['status']) . "</span>
                  </div>";
        }
    }
    $stmt->close();

    // Completed orders
    echo "<h3 style='margin-top: 30px;'>✅ Completed Orders (Today)</h3>";
    $stmt = $conn->prepare("
        SELECT r.id, u.first_name, u.last_name
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
        while($r = $result->fetch_assoc()){
            echo "<div class='order-item'>
                    <div class='order-info'>
                        Order #" . htmlspecialchars($r['id']) . " - " . htmlspecialchars($r['first_name'] . " " . $r['last_name']) . "
                    </div>
                    <span class='order-status status-completed'>Completed</span>
                  </div>";
        }
    }
    $stmt->close();
    ?>
</div>

</body>
</html>
