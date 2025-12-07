<?php
session_start();
include "db.php";

// Check if client is logged in
if(!isset($_SESSION['user_id'])){
    die("Not logged in");
}

$user_id = $_SESSION['user_id'];

// Cancel order if requested
if(isset($_GET['cancel_id'])){
    $cancel_id = intval($_GET['cancel_id']);
    $conn->query("DELETE FROM orders WHERE id=$cancel_id AND user_id='$user_id'");
    header("Location: client_orders.php");
    exit;
}

// Get client allergies
$u = $conn->query("SELECT allergies FROM users WHERE id=$user_id")->fetch_assoc();
$allergies = array_map('trim', explode(",", strtolower($u['allergies'])));

$data = $conn->query("
    SELECT o.id, o.food_id, o.status, o.created_at, u.username 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.user_id='$user_id'
    ORDER BY o.created_at DESC
");

echo "<a href='client_dashboard.php' style='color:blue;'>&larr; Back</a>";
echo "<h2>My Orders</h2>";

if($data->num_rows == 0){
    echo "No orders yet.";
} else {
    while($row = $data->fetch_assoc()){
        // Split food_ids if multiple
        
        $food_ids = array_map('intval', explode(",", $row['food_id']));

        $food_names = [];
        $total_price = 0;
        $allergen_warning = [];

        foreach($food_ids as $fid){
            $f = $conn->query("SELECT name, ingredients, price FROM food WHERE id='$fid'")->fetch_assoc();
            if($f){
                $food_names[] = $f['name'];
                $total_price += floatval($f['price']);

                // Check allergens
                foreach($allergies as $a){
                    if($a != "" && stripos($f['ingredients'], $a) !== false){
                        $allergen_warning[] = $a;
                    }
                }
            }
        }

        $food_list = implode(", ", $food_names);
        $allergen_list = !empty($allergen_warning) ? "<br><b style='color:red'>⚠ Contains allergens: " . implode(", ", array_unique($allergen_warning)) . "</b>" : "";

        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0; border-radius:8px;'>
                <strong>Order ID:</strong> {$row['id']}<br>
                <strong>Client:</strong> {$row['username']}<br>
                <strong>Foods:</strong> {$food_list}{$allergen_list}<br>
                <strong>Total:</strong> ₱{$total_price}<br>
                <strong>Status:</strong> {$row['status']}<br>
                <strong>Time:</strong> {$row['created_at']}<br>
                <a href='client_orders.php?cancel_id={$row['id']}' style='color:red; font-weight:bold;'>Cancel Order</a>
              </div>";
    }
}
?>
