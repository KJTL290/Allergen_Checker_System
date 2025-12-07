<?php
include "db.php";

// Handle Add Food
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $ingredients = $_POST['ingredients'];
    $price = $_POST['price'];

    $image = "";
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $target = "uploads/".basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $image = $_FILES['image']['name'];
    }

    $conn->query("INSERT INTO food(name, ingredients, price, image) 
                  VALUES('$name','$ingredients','$price','$image')");
}

// Handle Delete
if(isset($_GET['del'])){
    $conn->query("DELETE FROM food WHERE id=".$_GET['del']);
}

// Handle Edit
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $ingredients = $_POST['ingredients'];
    $price = $_POST['price'];

    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $target = "uploads/".basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $image = $_FILES['image']['name'];
        $conn->query("UPDATE food SET name='$name', ingredients='$ingredients', price='$price', image='$image' WHERE id=$id");
    } else {
        $conn->query("UPDATE food SET name='$name', ingredients='$ingredients', price='$price' WHERE id=$id");
    }
}
?>

<a href="admin_dashboard.php" style="color:blue; display:inline-block; margin-bottom:15px;">&larr; Back</a>

<h2>Add Food</h2>
<form method="post" enctype="multipart/form-data" style="margin-bottom:20px;">
    Name: <input name="name" required><br>
    Ingredients: <textarea name="ingredients" required></textarea><br>
    Price: <input name="price" required><br>
    Image: <input type="file" name="image"><br>
    <button name="add" style="padding:10px 20px; margin-top:5px;">Add</button>
</form>

<h2>Food List</h2>
<div class="food-container">
<?php
$data = $conn->query("SELECT * FROM food");
while($row = $data->fetch_assoc()){
    $img = isset($row['image']) && $row['image'] != "" ? "<img src='uploads/".$row['image']."' style='width:100%; border-radius:8px; margin-bottom:10px;'>" : "";

    echo "<div class='food-card'>
            $img
            <form method='post' enctype='multipart/form-data'>
                <input type='hidden' name='id' value='{$row['id']}'>
                Name: <input name='name' value='{$row['name']}' required><br>
                Ingredients: <textarea name='ingredients' required>{$row['ingredients']}</textarea><br>
                Price: <input name='price' value='{$row['price']}' required><br>
                Image: <input type='file' name='image'><br>
                <button name='edit' class='btn'>Update</button>
                <a href='?del={$row['id']}' class='btn delete'>Delete</a>
            </form>
          </div>";
}
?>
</div>

<!-- Responsive CSS -->
<style>
body { font-family: Arial, sans-serif; padding:15px; background:#f5f5f5; margin:0; }
a { cursor:pointer; text-decoration:none; }

/* Food container */
.food-container {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  justify-content: center;
}

/* Food card */
.food-card {
  border: 2px solid #ccc;
  border-radius: 12px;
  padding: 15px;
  background: #fff;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  flex: 1 1 250px;
  max-width: 300px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
}

/* Buttons */
.btn {
  display: inline-block;
  margin-top: 10px;
  padding: 10px;
  background: #007bff;
  color: #fff;
  border-radius: 8px;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  border: none;
}

.btn.delete {
  background: #dc3545;
  margin-left: 5px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .food-card { flex: 1 1 45%; }
}

@media (max-width: 480px) {
  .food-card { flex: 1 1 100%; }
}
</style>
