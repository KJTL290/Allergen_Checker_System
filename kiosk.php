<?php
include "db.php"; 
session_start();
$uid = $_SESSION['user_id'];

//$uid = $_SESSION['id'];

// Get client allergies
$u = $conn->query("SELECT allergies FROM users WHERE id=$uid")->fetch_assoc();
$allergies = array_map('trim', explode(",", strtolower($u['allergies'])));

if(isset($_POST['order_foods'])){
    $selected_foods = $_POST['food_ids'] ?? [];
    if(!empty($selected_foods)){
        // Remove duplicates and trim each ID
        $selected_foods = array_map('intval', $selected_foods); // ensure numeric
      //$selected_foods = array_map('trim', array_unique($selected_foods));
        // Combine all selected food IDs into one order entry (comma-separated)
        $food_ids_str = implode(",", $selected_foods);
        $conn->query("INSERT INTO orders (user_id, food_id, status, created_at) VALUES ('$uid', '$food_ids_str', 'Pending', NOW())");
        header("Location: kiosk.php?ordered=1");
        exit;
    }
}
?>

<a href="client_dashboard.php" style="color:blue; display:inline-block; margin-bottom:15px;">&larr; Back</a>

<?php
if(isset($_GET['ordered']) && $_GET['ordered'] == 1){
    echo "<p id='notify' style='color:green; font-weight:bold;'>✅ Your order has been placed!</p>";
}
?>

<form method="post">
<div class="food-container">
<?php
$foods = $conn->query("SELECT * FROM food");
while($f = $foods->fetch_assoc()){
    $warn = "";
    $highlight = "";

    foreach($allergies as $a){
        if($a != "" && stripos($f['ingredients'], $a) !== false){
            $warn = "<b style='color:red'>⚠ Contains: $a</b>";
            $highlight = "allergen";
        }
    }

    echo "<div class='food-card $highlight'>
            <h3>{$f['name']} – ₱{$f['price']}</h3>
            <p>{$f['ingredients']}</p>
            $warn
            <label><input type='checkbox' name='food_ids[]' value='{$f['id']}'> Select</label>
          </div>";
}
?>
</div>
<button type="submit" name="order_foods" style="margin-top:15px; padding:10px 20px;">Place Order</button>
</form>

<script>
  const notify = document.getElementById('notify');
  if(notify){
      setTimeout(() => { notify.style.display = 'none'; }, 3000);
  }
</script>
