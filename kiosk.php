<?php
include "db.php"; 
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

// Get client allergies
$stmt = $conn->prepare("SELECT allergies FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
$u = $result->fetch_assoc();
$stmt->close();

$allergies = $u && $u['allergies'] ? array_map('trim', explode(",", strtolower($u['allergies']))) : [];

if(isset($_POST['order_foods'])){
    $selected_foods = $_POST['food_ids'] ?? [];
    if(!empty($selected_foods)){
        // Sanitize and validate food IDs
        $selected_foods = array_map('intval', $selected_foods);
        $selected_foods = array_filter($selected_foods, function($id) { return $id > 0; });
        
        if(!empty($selected_foods)){
            $food_ids_str = implode(",", $selected_foods);
            
            $stmt = $conn->prepare("INSERT INTO orders (user_id, food_id, status, created_at) VALUES (?, ?, 'Pending', NOW())");
            $stmt->bind_param("is", $uid, $food_ids_str);
            $stmt->execute();
            $stmt->close();
            
            header("Location: kiosk.php?ordered=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Food Ordering - Allergen Checker</title>
<link rel="stylesheet" href="styles.css">
<style>
.kiosk-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
.back-link { display: inline-block; margin-bottom: 20px; color: #3A8DFF; text-decoration: none; font-weight: bold; }
.back-link:hover { text-decoration: underline; }
#notify { padding: 15px; margin: 20px 0; border-radius: 8px; font-weight: bold; }
.food-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin: 30px 0; }
.food-card { border: 2px solid #ddd; border-radius: 10px; padding: 15px; background: #fff; transition: all 0.3s; }
.food-card.allergen { border-color: #ff6b6b; background: #fff5f5; }
.food-card h3 { margin: 0 0 10px 0; color: #333; }
.food-card p { margin: 8px 0; color: #666; font-size: 14px; }
.food-card label { display: flex; align-items: center; margin-top: 15px; cursor: pointer; }
.food-card input[type="checkbox"] { margin-right: 10px; cursor: pointer; }
.warning { color: #dc3545; font-weight: bold; margin-top: 10px; }
.order-button { margin-top: 30px; padding: 12px 30px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
.order-button:hover { background: #218838; }
</style>
</head>
<body>

<div class="kiosk-container">
    <a href="client_dashboard.php" class="back-link">&larr; Back</a>

    <?php if(isset($_GET['ordered']) && $_GET['ordered'] == 1): ?>
        <div id="notify" style="color: #155724; background: #d4edda; border: 1px solid #c3e6cb;">
            ✅ Your order has been placed successfully!
        </div>
        <script>
            setTimeout(() => { document.getElementById('notify').style.display = 'none'; }, 4000);
        </script>
    <?php endif; ?>

    <h2>Order Food</h2>
    <p style="color: #666; margin-bottom: 20px;">Select the food items you want to order. Items highlighted in red contain allergens from your profile.</p>

    <form method="post">
        <div class="food-container">
            <?php
            $stmt = $conn->prepare("SELECT id, name, price, ingredients FROM food ORDER BY name ASC");
            $stmt->execute();
            $foods = $stmt->get_result();
            
            while($f = $foods->fetch_assoc()):
                $warn = "";
                $highlight = "";
                $allergen_list = [];

                foreach($allergies as $a){
                    if($a != "" && stripos($f['ingredients'], $a) !== false){
                        $allergen_list[] = ucfirst($a);
                        $highlight = "allergen";
                    }
                }

                if(!empty($allergen_list)){
                    $warn = "<div class='warning'>⚠ Contains: " . implode(", ", $allergen_list) . "</div>";
                }
            ?>
                <div class="food-card <?php echo $highlight; ?>">
                    <h3><?php echo htmlspecialchars($f['name']); ?> – ₱<?php echo number_format($f['price'], 2); ?></h3>
                    <p><strong>Ingredients:</strong> <?php echo htmlspecialchars($f['ingredients']); ?></p>
                    <?php echo $warn; ?>
                    <label>
                        <input type="checkbox" name="food_ids[]" value="<?php echo $f['id']; ?>">
                        Select this item
                    </label>
                </div>
            <?php
            endwhile;
            $stmt->close();
            ?>
        </div>

        <button type="submit" name="order_foods" class="order-button">Place Order</button>
    </form>
</div>

</body>
</html>
