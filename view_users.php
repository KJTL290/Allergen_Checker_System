<?php
include "db.php";

// Handle Delete
if(isset($_GET['del'])){
    $conn->query("DELETE FROM users WHERE id=".$_GET['del']);
}

// Handle Edit
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $allergies = $_POST['allergies'];

    $conn->query("UPDATE users SET username='$username', role='$role', allergies='$allergies' WHERE id=$id");
}
?>

<a href="admin_dashboard.php" style="color:blue; display:inline-block; margin-bottom:15px;">&larr; Back</a>

<h2>Manage Users</h2>

<?php
$data = $conn->query("SELECT * FROM users WHERE role != 'admin'");
while($row = $data->fetch_assoc()){
    echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:8px; background:#fff;'>
            <form method='post' style='margin:0;'>
                <input type='hidden' name='id' value='{$row['id']}'>
                Username: <input name='username' value='{$row['username']}' required><br>
                Role: <select name='role'>
                        <option value='staff' ".($row['role']=='staff'?'selected':'').">Staff</option>
                        <option value='client' ".($row['role']=='client'?'selected':'').">Client</option>
                      </select><br>
                Allergies (comma-separated): <input name='allergies' value='{$row['allergies']}'><br>
                <button name='edit' style='padding:5px 10px; margin-top:5px;'>Update</button>
                <a href='?del={$row['id']}' style='color:red; padding:5px 10px; background:#f5f5f5; border-radius:5px; text-decoration:none;'>Delete</a>
            </form>
          </div>";
}
?>
