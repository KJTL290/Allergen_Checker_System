<?php
session_start();
if($_SESSION['role'] != 'admin'){ header("location: login.php"); exit; }
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<!--
<style>
body { font-family: Arial; padding:20px; }
.card { padding:15px; background:#f0f0f0; border-radius:8px; margin-bottom:10px; }
a { text-decoration:none; color:#007bff; }
</style>
-->
<link rel="stylesheet" href="style.css">

</head>
<body>

<h2>Admin Dashboard</h2>

<a href="logout.php" style="color:red; font-weight:bold;">Logout</a>

<div class="card"><a href="admin_food.php">Manage Food Menu (CRUD)</a></div>
<div class="card"><a href="view_users.php">Manage Users (CRUD)</a></div>
<div class="card"><a href="queue.php">View Order Queue</a></div>

<div class="card"><a href="staff_register.php">Create Staff Account</a></div>

<div class="card"><a href="admin_payments.php">View Payments</a></div>

</body>
</html>
