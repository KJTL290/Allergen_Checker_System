<?php
session_start();
if($_SESSION['role'] != 'client'){ header("location: login.php"); exit; }
?>

<!DOCTYPE html>
<html>
<head>
<title>Client Dashboard</title>
<style>
body { font-family: Arial; padding:20px; }
.card { padding:15px; background:#f0f0f0; border-radius:8px; margin-bottom:10px; }
a { text-decoration:none; color:#007bff; }
</style>
</head>
<body>

<h2>Client Dashboard</h2>
<a href="logout.php" style="color:red; font-weight:bold;">Logout</a>

<div class="card"><a href="client_profile.php">My Allergies</a></div>
<div class="card"><a href="kiosk.php">Order Food (Kiosk)</a></div>
<!--<div class="card"><a href="queue.php">My Order Status</a></div>-->
<div class="card"><a href="client_orders.php">My Order Status</a></div>

</body>
</html>
