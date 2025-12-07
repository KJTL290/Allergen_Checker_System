<?php
include "db.php";
session_start();

if($_SESSION['role'] != 'admin'){
    header("location: login.php");
    exit;
}

// Handle Add Food
if(isset($_POST['add'])){
    $name = trim($_POST['name']);
    $ingredients = trim($_POST['ingredients']);
    $price = floatval($_POST['price']);

    $image = "";
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        // Create uploads directory if it doesn't exist
        if(!is_dir("uploads")) mkdir("uploads", 0755, true);
        
        $target = "uploads/".basename($_FILES['image']['name']);
        if(move_uploaded_file($_FILES['image']['tmp_name'], $target)){
            $image = $_FILES['image']['name'];
        }
    }

    $stmt = $conn->prepare("INSERT INTO food(name, ingredients, price, image) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $ingredients, $price, $image);
    $stmt->execute();
    $stmt->close();
    
    $message = "Food item added successfully!";
}

// Handle Delete
if(isset($_GET['del'])){
    $del_id = intval($_GET['del']);
    $stmt = $conn->prepare("DELETE FROM food WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin_food.php?deleted=1");
    exit;
}

// Handle Edit
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $ingredients = trim($_POST['ingredients']);
    $price = floatval($_POST['price']);

    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        if(!is_dir("uploads")) mkdir("uploads", 0755, true);
        
        $target = "uploads/".basename($_FILES['image']['name']);
        if(move_uploaded_file($_FILES['image']['tmp_name'], $target)){
            $image = $_FILES['image']['name'];
            $stmt = $conn->prepare("UPDATE food SET name = ?, ingredients = ?, price = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdsi", $name, $ingredients, $price, $image, $id);
        } else {
            $stmt = $conn->prepare("UPDATE food SET name = ?, ingredients = ?, price = ? WHERE id = ?");
            $stmt->bind_param("sdsi", $name, $ingredients, $price, $id);
        }
    } else {
        $stmt = $conn->prepare("UPDATE food SET name = ?, ingredients = ?, price = ? WHERE id = ?");
        $stmt->bind_param("sdsi", $name, $ingredients, $price, $id);
    }
    
    $stmt->execute();
    $stmt->close();
    
    $message = "Food item updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Food Menu - Admin</title>
<link rel="stylesheet" href="styles.css">
<style>
body { font-family: Arial, sans-serif; padding:15px; background:#f5f5f5; margin:0; }
.admin-container { max-width: 1200px; margin: 0 auto; }
a { cursor:pointer; text-decoration:none; }
.back-link { display: inline-block; margin-bottom: 20px; color: #3A8DFF; font-weight: bold; }
.back-link:hover { text-decoration: underline; }
.message { padding: 12px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px; }

.add-section { background: #fff; padding: 25px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.add-section h3 { margin-top: 0; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
.form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
.form-group textarea { resize: vertical; min-height: 80px; }
.btn-add { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
.btn-add:hover { background: #218838; }

.food-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.food-card {
  border: 2px solid #ddd;
  border-radius: 12px;
  padding: 20px;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: all 0.3s;
}

.food-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

.food-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 10px;
}

.food-card h4 { margin: 10px 0; color: #333; }
.food-card p { margin: 8px 0; font-size: 13px; color: #666; }
.food-card input, .food-card textarea { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
.food-card textarea { min-height: 60px; }

.btn-group { margin-top: 10px; display: flex; gap: 5px; }
.btn { display: inline-block; padding: 8px 12px; border-radius: 5px; text-align: center; text-decoration: none; cursor: pointer; border: none; font-weight: bold; }
.btn-update { background: #007bff; color: white; flex: 1; }
.btn-update:hover { background: #0056b3; }
.btn-delete { background: #dc3545; color: white; }
.btn-delete:hover { background: #c82333; }

@media (max-width: 768px) {
  .food-container { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
}

@media (max-width: 480px) {
  .food-container { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div class="admin-container">
    <a href="admin_dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    
    <?php if(isset($message)) echo "<div class='message'>✓ $message</div>"; ?>
    <?php if(isset($_GET['deleted'])) echo "<div class='message'>✓ Food item deleted successfully!</div>"; ?>

    <div class="add-section">
        <h3>➕ Add New Food Item</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Food Name *</label>
                <input type="text" name="name" required placeholder="e.g., Burger">
            </div>
            <div class="form-group">
                <label>Ingredients *</label>
                <textarea name="ingredients" required placeholder="e.g., Bun, meat, lettuce, cheese"></textarea>
            </div>
            <div class="form-group">
                <label>Price (₱) *</label>
                <input type="number" name="price" required step="0.01" min="0" placeholder="e.g., 25.00">
            </div>
            <div class="form-group">
                <label>Food Image</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <button type="submit" name="add" class="btn-add">Add Food Item</button>
        </form>
    </div>

    <h3>📋 Food Menu</h3>
    <div class="food-container">
        <?php
        $stmt = $conn->prepare("SELECT id, name, ingredients, price, image FROM food ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        
        while($row = $result->fetch_assoc()){
            $img = isset($row['image']) && $row['image'] != "" ? "<img src='uploads/".htmlspecialchars($row['image'])."' alt='".htmlspecialchars($row['name'])."'>" : "<div style='width:100%; height:200px; background:#f0f0f0; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#999;'>No Image</div>";

            echo "<div class='food-card'>
                    $img
                    <form method='post' enctype='multipart/form-data'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <h4>Name</h4>
                        <input type='text' name='name' value='".htmlspecialchars($row['name'])."' required>
                        
                        <h4>Ingredients</h4>
                        <textarea name='ingredients' required>".htmlspecialchars($row['ingredients'])."</textarea>
                        
                        <h4>Price (₱)</h4>
                        <input type='number' name='price' value='".htmlspecialchars($row['price'])."' required step='0.01' min='0'>
                        
                        <h4>Update Image</h4>
                        <input type='file' name='image' accept='image/*'>
                        <small style='color:#999;'>Leave blank to keep current image</small>
                        
                        <div class='btn-group'>
                            <button type='submit' name='edit' class='btn btn-update'>Update</button>
                            <a href='?del={$row['id']}' class='btn btn-delete' onclick=\"return confirm('Delete this food item?')\">Delete</a>
                        </div>
                    </form>
                  </div>";
        }
        $stmt->close();
        ?>
    </div>
</div>

</body>
</html>
