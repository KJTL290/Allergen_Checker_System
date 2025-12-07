<?php
session_start();
if($_SESSION['role'] != 'staff'){ header("location: login.php"); exit; }
?>

<!DOCTYPE html>
<html>
<head>
<title>Staff Dashboard</title>
<style>
body { font-family: Arial; padding:20px; }
.card { padding:15px; background:#f0f0f0; border-radius:8px; margin-bottom:10px; }
a { text-decoration:none; color:#007bff; }
</style>
</head>
<body>

<h2>Staff Dashboard</h2>
<a href="logout.php" style="color:red; font-weight:bold;">Logout</a>

<div class="card"><a href="staff_queue.php">View Order Queue</a></div>
<div class="card"><a href="update_order.php">Update Order Status</a></div>

</body>
</html>
