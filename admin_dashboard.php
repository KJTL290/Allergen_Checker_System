<?php
session_start();
if($_SESSION['role'] != 'admin'){ 
    header("location: login.php"); 
    exit; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Allergen Checker</title>
<link rel="stylesheet" href="styles.css">
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 0; margin: 0; }
.dashboard { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #ddd; }
.header h1 { margin: 0; color: #333; }
.logout-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; }
.logout-btn:hover { background: #c82333; }
.cards-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
.card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s; text-decoration: none; color: inherit; display: block; }
.card:hover { transform: translateY(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.card h3 { margin-top: 0; color: #3A8DFF; font-size: 18px; }
.card p { margin: 10px 0 0 0; color: #666; font-size: 14px; }
.card-icon { font-size: 32px; margin-bottom: 10px; }
.section-title { margin-top: 40px; margin-bottom: 20px; font-size: 20px; color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
</style>
</head>
<body>

<div class="dashboard">
    <div class="header">
        <h1>⚙️ Admin Dashboard</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="section-title">📊 Operations</div>
    <div class="cards-container">
        <a href="queue.php" class="card">
            <div class="card-icon">📋</div>
            <h3>Order Queue</h3>
            <p>View all pending, accepted, and completed orders in the system</p>
        </a>

        <a href="admin_payments.php" class="card">
            <div class="card-icon">💰</div>
            <h3>Sales Reports</h3>
            <p>View completed orders and sales by date with analytics</p>
        </a>
    </div>

    <div class="section-title">🍽️ Content Management</div>
    <div class="cards-container">
        <a href="admin_food.php" class="card">
            <div class="card-icon">🍔</div>
            <h3>Manage Food Menu</h3>
            <p>Create, edit, and delete food items, ingredients, and prices</p>
        </a>

        <a href="view_users.php" class="card">
            <div class="card-icon">👥</div>
            <h3>Manage Users</h3>
            <p>Edit client and staff accounts, manage allergies and roles</p>
        </a>
    </div>

    <div class="section-title">👨‍💼 Staff Management</div>
    <div class="cards-container">
        <a href="staff_register.php" class="card">
            <div class="card-icon">➕</div>
            <h3>Create Staff Account</h3>
            <p>Create new staff accounts for cashiers and food handlers</p>
        </a>
    </div>
</div>

</body>
</html>
