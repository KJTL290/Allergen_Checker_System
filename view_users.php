<?php
include "db.php";
session_start();

if($_SESSION['role'] != 'admin'){
    header("location: login.php");
    exit;
}

// Handle Delete
if(isset($_GET['del'])){
    $del_id = intval($_GET['del']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: view_users.php?deleted=1");
    exit;
}

// Handle Edit
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $contact_info = trim($_POST['contact_info'] ?? '');
    $allergies = trim($_POST['allergies'] ?? '');

    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, first_name = ?, middle_name = ?, last_name = ?, age = ?, contact_info = ?, allergies = ? WHERE id = ?");
    $stmt->bind_param("ssssisisi", $username, $role, $first_name, $middle_name, $last_name, $age, $contact_info, $allergies, $id);
    $stmt->execute();
    $stmt->close();
    
    $message = "User updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users - Admin</title>
<link rel="stylesheet" href="styles.css">
<style>
.users-container { max-width: 1200px; margin: 20px auto; padding: 20px; }
.back-link { display: inline-block; margin-bottom: 20px; color: #3A8DFF; text-decoration: none; font-weight: bold; }
.back-link:hover { text-decoration: underline; }
.message { padding: 12px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px; }
.user-card { border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 10px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.user-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
.user-name { font-weight: bold; font-size: 16px; }
.user-role { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; }
.role-admin { background: #dc3545; color: white; }
.role-staff { background: #ffc107; color: black; }
.role-client { background: #17a2b8; color: white; }
.form-group { margin-bottom: 12px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px; }
.form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.btn-group { display: flex; gap: 10px; margin-top: 15px; }
.btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
.btn-update { background: #007bff; color: white; flex: 1; }
.btn-update:hover { background: #0056b3; }
.btn-delete { background: #dc3545; color: white; }
.btn-delete:hover { background: #c82333; }

@media (max-width: 768px) {
    .form-row { grid-template-columns: 1fr; }
    .user-header { flex-direction: column; align-items: flex-start; }
}
</style>
</head>
<body>

<div class="users-container">
    <a href="admin_dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    
    <?php if(isset($message)) echo "<div class='message'>✓ $message</div>"; ?>
    <?php if(isset($_GET['deleted'])) echo "<div class='message'>✓ User deleted successfully!</div>"; ?>

    <h2>👥 Manage All Users</h2>

    <?php
    $stmt = $conn->prepare("SELECT id, username, role, first_name, middle_name, last_name, age, contact_info, allergies FROM users WHERE role != 'admin' ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()){
        $role_class = "role-" . $row['role'];
        $full_name = trim(($row['first_name'] ?? '') . " " . ($row['middle_name'] ?? '') . " " . ($row['last_name'] ?? ''));
        $display_name = $full_name ?: htmlspecialchars($row['username']);
        
        echo "<div class='user-card'>
                <div class='user-header'>
                    <span class='user-name'>$display_name</span>
                    <span class='user-role $role_class'>" . strtoupper($row['role']) . "</span>
                </div>
                
                <form method='post' style='margin:0;'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    
                    <div class='form-row'>
                        <div class='form-group'>
                            <label>Username</label>
                            <input type='text' name='username' value='".htmlspecialchars($row['username'])."' required>
                        </div>
                        <div class='form-group'>
                            <label>Role</label>
                            <select name='role'>
                                <option value='staff' " . ($row['role']=='staff'?'selected':'') . ">Staff</option>
                                <option value='client' " . ($row['role']=='client'?'selected':'') . ">Client</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class='form-row'>
                        <div class='form-group'>
                            <label>First Name</label>
                            <input type='text' name='first_name' value='".htmlspecialchars($row['first_name'] ?? '')."'>
                        </div>
                        <div class='form-group'>
                            <label>Middle Name</label>
                            <input type='text' name='middle_name' value='".htmlspecialchars($row['middle_name'] ?? '')."'>
                        </div>
                    </div>
                    
                    <div class='form-row'>
                        <div class='form-group'>
                            <label>Last Name</label>
                            <input type='text' name='last_name' value='".htmlspecialchars($row['last_name'] ?? '')."'>
                        </div>
                        <div class='form-group'>
                            <label>Age</label>
                            <input type='number' name='age' value='".htmlspecialchars($row['age'] ?? '')."' min='1' max='120'>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label>Contact Info (Phone)</label>
                        <input type='tel' name='contact_info' value='".htmlspecialchars($row['contact_info'] ?? '')."'>
                    </div>
                    
                    <div class='form-group'>
                        <label>Allergies (comma-separated)</label>
                        <input type='text' name='allergies' value='".htmlspecialchars($row['allergies'] ?? '')."' placeholder='e.g., Dairy,Gluten,Peanuts'>
                    </div>
                    
                    <div class='btn-group'>
                        <button type='submit' name='edit' class='btn btn-update'>Update User</button>
                        <a href='?del={$row['id']}' class='btn btn-delete' onclick=\"return confirm('Are you sure you want to delete this user?')\">Delete</a>
                    </div>
                </form>
              </div>";
    }
    $stmt->close();
    ?>
</div>

</body>
</html>
