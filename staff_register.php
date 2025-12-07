<?php
include "db.php";
session_start();

if($_SESSION['role'] != 'admin'){
    header("location: login.php");
    exit;
}

if(isset($_POST['register'])){
    $u = trim($_POST['username']);
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fn = trim($_POST['first_name'] ?? '');
    $ln = trim($_POST['last_name'] ?? '');

    $stmt = $conn->prepare("INSERT INTO users(username, password, role, first_name, last_name) VALUES(?, ?, 'staff', ?, ?)");
    $stmt->bind_param("ssss", $u, $p, $fn, $ln);
    if($stmt->execute()){
        $success = "Staff account created successfully!";
    } else {
        $error = "Failed to create staff account. Username may already exist.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Staff Account</title>
<link rel="stylesheet" href="styles.css">
<style>
.staff-form { max-width: 500px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.staff-form h2 { text-align: center; margin-bottom: 30px; }
.staff-form input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
.staff-form button { width: 100%; padding: 12px; background: #3A8DFF; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
.staff-form button:hover { background: #3273d6; }
.staff-form a { display: block; text-align: center; margin-top: 15px; color: #3A8DFF; text-decoration: none; }
.success { color: green; text-align: center; font-weight: bold; background: #d4edda; padding: 10px; border-radius: 5px; }
.error { color: red; text-align: center; font-weight: bold; background: #f8d7da; padding: 10px; border-radius: 5px; }
</style>
</head>
<body>

<div class="staff-form">
    <h2>Create Staff Account</h2>

    <?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="first_name" placeholder="First Name (Optional)">
        <input type="text" name="last_name" placeholder="Last Name (Optional)">
        <button type="submit" name="register">Create Staff Account</button>
    </form>

    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
