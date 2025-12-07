<?php
session_start();
include "db.php";

// Only staff can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    die("Access denied.");
}
?>

<a href="staff_dashboard.php" style="color:blue;">&larr; Back</a>
<h2>Order Queue</h2>

<?php
$data = $conn->query("
    SELECT o.id, o.user_id, o.food_id, o.created_at, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at ASC
");

if($data->num_rows == 0){
    echo "No pending orders.";
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
                <strong>Time:</strong> {$row['created_at']}<br>
                <a href='update_order.php?receive_id={$row['id']}' style='color:green; font-weight:bold;'>Receive Payment</a>
              </div>";
    }
}
?>
