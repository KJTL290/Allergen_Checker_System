<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Cancel order if requested
if(isset($_GET['cancel_id'])){
    $cancel_id = intval($_GET['cancel_id']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cancel_id, $user_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: client_orders.php");
    exit;
}

// Get client allergies
$stmt = $conn->prepare("SELECT allergies FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$u = $result->fetch_assoc();
$stmt->close();

$allergies = $u && $u['allergies'] ? array_map('trim', explode(",", strtolower($u['allergies']))) : [];

$stmt = $conn->prepare("
    SELECT o.id, o.food_id, o.status, o.created_at, u.username, u.first_name, u.last_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders - Allergen Checker</title>
<link rel="stylesheet" href="styles.css">
<style>
.orders-container { max-width: 900px; margin: 20px auto; padding: 20px; }
.back-link { display: inline-block; margin-bottom: 20px; color: #3A8DFF; text-decoration: none; font-weight: bold; }
.back-link:hover { text-decoration: underline; }
.order-card { border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 8px; background: #f9f9f9; }
.order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
.order-id { font-weight: bold; color: #333; }
.order-status { padding: 5px 12px; border-radius: 5px; font-size: 13px; }
.status-pending { background: #fff3cd; color: #856404; }
.status-completed { background: #d4edda; color: #155724; }
.order-details { margin: 10px 0; }
.order-detail-row { display: flex; justify-content: space-between; margin: 5px 0; }
.allergen-warning { color: #dc3545; font-weight: bold; margin-top: 8px; }
.cancel-btn { background: #dc3545; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
.cancel-btn:hover { background: #c82333; }
.no-orders { text-align: center; padding: 40px 20px; color: #666; }
</style>
</head>
<body>

<div class="orders-container">
    <a href="client_dashboard.php" class="back-link">&larr; Back to Dashboard</a>

    <h2>My Orders</h2>

    <?php
    if($result->num_rows == 0){
        echo "<div class='no-orders'><p>You have no orders yet. <a href='kiosk.php' style='color: #3A8DFF;'>Start ordering now!</a></p></div>";
    } else {
        while($row = $result->fetch_assoc()){
            $food_ids = array_map('intval', explode(",", $row['food_id']));
            $food_names = [];
            $total_price = 0;
            $allergen_warning = [];

            foreach($food_ids as $fid){
                $food_stmt = $conn->prepare("SELECT name, ingredients, price FROM food WHERE id = ?");
                $food_stmt->bind_param("i", $fid);
                $food_stmt->execute();
                $food_result = $food_stmt->get_result();
                $f = $food_result->fetch_assoc();
                $food_stmt->close();
                
                if($f){
                    $food_names[] = htmlspecialchars($f['name']);
                    $total_price += floatval($f['price']);

                    foreach($allergies as $a){
                        if($a != "" && stripos($f['ingredients'], $a) !== false){
                            $allergen_warning[] = ucfirst($a);
                        }
                    }
                }
            }

            $food_list = implode(", ", $food_names);
            $allergen_list = !empty($allergen_warning) ? "<div class='allergen-warning'>⚠ Contains allergens: " . implode(", ", array_unique($allergen_warning)) . "</div>" : "";
            $status_class = $row['status'] === 'Completed' ? 'status-completed' : 'status-pending';
            
            echo "<div class='order-card'>
                    <div class='order-header'>
                        <span class='order-id'>Order #" . htmlspecialchars($row['id']) . "</span>
                        <span class='order-status $status_class'>" . htmlspecialchars($row['status']) . "</span>
                    </div>
                    <div class='order-details'>
                        <div class='order-detail-row'><strong>Foods:</strong> <span>$food_list</span></div>
                        <div class='order-detail-row'><strong>Total:</strong> <span>₱" . number_format($total_price, 2) . "</span></div>
                        <div class='order-detail-row'><strong>Ordered:</strong> <span>" . htmlspecialchars($row['created_at']) . "</span></div>
                        $allergen_list
                    </div>";
            
            if($row['status'] !== 'Completed'){
                echo "<a href='?cancel_id=" . htmlspecialchars($row['id']) . "' class='cancel-btn' onclick=\"return confirm('Are you sure you want to cancel this order?')\">Cancel Order</a>";
            }
            
            echo "</div>";
        }
    }
    $stmt->close();
    ?>
</div>

</body>
</html>
