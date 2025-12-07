<?php
session_start();
include "db.php";

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$selected_date = isset($_POST['select_date']) ? trim($_POST['select_date']) : date('Y-m-d');
$total_sales = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Reports - Admin</title>
<link rel="stylesheet" href="styles.css">
<style>
.sales-container { max-width: 1000px; margin: 20px auto; padding: 20px; }
.back-link { display: inline-block; margin-bottom: 20px; color: #3A8DFF; text-decoration: none; font-weight: bold; }
.back-link:hover { text-decoration: underline; }
.filter-section { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.filter-group { display: flex; gap: 10px; align-items: flex-end; }
.filter-group label { font-weight: bold; }
.filter-group input { padding: 8px 12px; border: 1px solid #ccc; border-radius: 5px; }
.filter-group button { padding: 8px 20px; background: #3A8DFF; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
.filter-group button:hover { background: #3273d6; }
.stats-section { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
.stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; }
.stat-value { font-size: 28px; font-weight: bold; }
.stat-label { font-size: 14px; opacity: 0.9; }
.order-card { border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 8px; background: #f9f9f9; }
.order-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
.order-id { font-weight: bold; }
.order-total { color: #28a745; font-weight: bold; font-size: 16px; }
.order-details { margin: 10px 0; }
.no-orders { text-align: center; padding: 40px; color: #666; }

@media (max-width: 768px) {
    .filter-group { flex-direction: column; }
    .stat-card { grid-column: 1; }
}
</style>
</head>
<body>

<div class="sales-container">
    <a href="admin_dashboard.php" class="back-link">&larr; Back to Dashboard</a>

    <h2>📊 Sales Reports & Completed Orders</h2>

    <!-- Date Filter -->
    <div class="filter-section">
        <form method="post">
            <div class="filter-group">
                <label for="select_date">Select Date:</label>
                <input type="date" id="select_date" name="select_date" value="<?php echo htmlspecialchars($selected_date); ?>">
                <button type="submit">View Sales</button>
            </div>
        </form>
    </div>

    <?php
    // Get sales for selected date
    $stmt = $conn->prepare("
        SELECT r.id, r.user_id, r.food_id, r.status, r.payment_status, r.created_at, u.username, u.first_name, u.last_name
        FROM received_orders r
        JOIN users u ON r.user_id = u.id
        WHERE r.status = 'Completed' AND DATE(r.created_at) = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->bind_param("s", $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $order_count = $result->num_rows;
    
    // Calculate total sales
    if($order_count > 0){
        $result->data_seek(0); // Reset pointer
        while($row = $result->fetch_assoc()){
            $food_ids = array_map('intval', explode(",", $row['food_id']));
            foreach($food_ids as $fid){
                $food_stmt = $conn->prepare("SELECT price FROM food WHERE id = ?");
                $food_stmt->bind_param("i", $fid);
                $food_stmt->execute();
                $food_result = $food_stmt->get_result();
                $f = $food_result->fetch_assoc();
                $food_stmt->close();
                
                if($f){
                    $total_sales += floatval($f['price']);
                }
            }
        }
        $result->data_seek(0); // Reset pointer again
    }

    // Stats Section
    if($order_count > 0){
        echo "<div class='stats-section'>
                <div class='stat-card'>
                    <div class='stat-value'>$order_count</div>
                    <div class='stat-label'>Total Orders</div>
                </div>
                <div class='stat-card'>
                    <div class='stat-value'>₱" . number_format($total_sales, 2) . "</div>
                    <div class='stat-label'>Total Sales</div>
                </div>
                <div class='stat-card'>
                    <div class='stat-value'>₱" . number_format($total_sales / $order_count, 2) . "</div>
                    <div class='stat-label'>Avg Order Value</div>
                </div>
              </div>";
    }

    // Orders List
    echo "<h3>Orders for " . date('F d, Y', strtotime($selected_date)) . "</h3>";
    
    if($result->num_rows == 0){
        echo "<div class='no-orders'><p>No completed orders on this date.</p></div>";
    } else {
        while($row = $result->fetch_assoc()){
            $food_ids = array_map('intval', explode(",", $row['food_id']));
            $food_names = [];
            $order_total = 0;

            foreach($food_ids as $fid){
                $food_stmt = $conn->prepare("SELECT name, price FROM food WHERE id = ?");
                $food_stmt->bind_param("i", $fid);
                $food_stmt->execute();
                $food_result = $food_stmt->get_result();
                $f = $food_result->fetch_assoc();
                $food_stmt->close();
                
                if($f){
                    $food_names[] = htmlspecialchars($f['name']);
                    $order_total += floatval($f['price']);
                }
            }

            $food_list = implode(", ", $food_names);
            $client_name = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
            
            echo "<div class='order-card'>
                    <div class='order-header'>
                        <span class='order-id'>Order #" . htmlspecialchars($row['id']) . " - $client_name</span>
                        <span class='order-total'>₱" . number_format($order_total, 2) . "</span>
                    </div>
                    <div class='order-details'>
                        <div><strong>Foods:</strong> $food_list</div>
                        <div><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</div>
                        <div><strong>Payment:</strong> " . htmlspecialchars($row['payment_status']) . "</div>
                        <div><strong>Completed:</strong> " . htmlspecialchars($row['created_at']) . "</div>
                    </div>
                  </div>";
        }
    }
    $stmt->close();
    ?>
</div>

</body>
</html>
