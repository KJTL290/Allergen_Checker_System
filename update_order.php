<?php
session_start();
include "db.php";

// Only staff can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    die("Access denied.");
}

// Receive order from orders table
if(isset($_GET['receive_id'])){
    $receive_id = intval($_GET['receive_id']);

    // Get order data
    $order = $conn->query("SELECT * FROM orders WHERE id=$receive_id")->fetch_assoc();
    if($order){
        $user_id = $order['user_id'];
        $food_id = $order['food_id'];

        // Move to received_orders
        $conn->query("INSERT INTO received_orders (user_id, food_id, status, payment_status, created_at)
                      VALUES ('$user_id', '$food_id', 'Pending', 'Unpaid', NOW())");

        // Delete from orders table
        $conn->query("DELETE FROM orders WHERE id=$receive_id");
    }
    header("Location: update_order.php"); // Refresh page so order disappears
    exit;
}

// Complete order (mark payment complete)
if(isset($_GET['complete_id'])){
    $complete_id = intval($_GET['complete_id']);
    $conn->query("UPDATE received_orders SET status='Completed', payment_status='Paid' WHERE id=$complete_id");
    header("Location: update_order.php"); // Refresh page so completed order disappears from staff view
    exit;
}
?>

<a href="staff_dashboard.php" style="color:blue;">&larr; Back</a>
<h2>Orders Received / Payment</h2>

<?php
// Only fetch orders that are not completed
$data = $conn->query("
    SELECT r.id, r.user_id, r.food_id, r.status, r.payment_status, r.created_at, u.username
    FROM received_orders r
    JOIN users u ON r.user_id = u.id
    WHERE r.status != 'Completed'
    ORDER BY r.created_at ASC
");

if($data->num_rows == 0){
    echo "No received orders.";
} else {
    while($row = $data->fetch_assoc()){
        $food_ids = explode(",", $row['food_id']);
        $food_names = [];
        foreach($food_ids as $fid){
            $f = $conn->query("SELECT name FROM food WHERE id='$fid'")->fetch_assoc();
            if($f) $food_names[] = $f['name'];
        }
        $food_list = implode(", ", $food_names);

        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0; border-radius:8px;'>
                <strong>Order ID:</strong> {$row['id']}<br>
                <strong>Client:</strong> {$row['username']}<br>
                <strong>Foods:</strong> {$food_list}<br>
                <strong>Status:</strong> {$row['status']}<br>
                <strong>Payment:</strong> {$row['payment_status']}<br>
                <strong>Time:</strong> {$row['created_at']}<br>
                <a href='update_order.php?complete_id={$row['id']}' style='color:green; font-weight:bold;'>Complete Order</a>
              </div>";
    }
}
?>
