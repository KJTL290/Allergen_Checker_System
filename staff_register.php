<?php
include "db.php";
session_start();

if($_SESSION['role'] != 'admin'){
    header("location: login.php");
    exit;
}

if(isset($_POST['register'])){
    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // staff account
    $conn->query("INSERT INTO users(username,password,role)
                  VALUES('$u','$p','staff')");
    echo "Staff account created!";
}
?>

<h2>Create Staff Account (Admin Only)</h2>

<form method="post">
  Username <input name="username" required><br>
  Password <input type="password" name="password" required><br>
  <button name="register">Create Staff</button>
</form>

<a href="admin_dashboard.php">Back</a>
