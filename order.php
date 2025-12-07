<?php
include "db.php"; 
session_start();
$uid = $_SESSION['id'];
$fid = $_GET['food_id'];

$conn->query("INSERT INTO orders(user_id,food_id) VALUES($uid,$fid)");

// Redirect back to kiosk with success message
header("location: kiosk.php?ordered=1");
exit;
?>
