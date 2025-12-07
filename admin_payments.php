<?php
session_start();
include "db.php";

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    die("Access denied.");
}
?>

<a href="admin_dashboard.php" style="color:blue;">&larr; Back</a>
<h2>Completed Orders & Payments</h2>

<?php
$data = $conn->query("
    SELECT r.id, r.user_id, r.food_id, r.status, r.payment_status, r.created_at, u.username
    FROM received_orders r
    JOIN users u ON r.user_id = u.id
    WHERE r.status='Completed'
    ORDER BY r.created_at DESC
");

if($data->num_rows == 0){
    echo "No completed orders yet.";
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
                <strong>Time:</strong> {$row['created_at']}
              </div>";
    }
}
?>
