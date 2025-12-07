<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    die("Not logged in");
}

$user_id = $_SESSION['user_id'];

// Delete profile
if(isset($_GET['delete_profile'])){
    if($_GET['delete_profile'] == 'confirm'){
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        session_destroy();
        header("Location: login.php?msg=Profile deleted successfully");
        exit;
    }
}

// Fetch user profile
$stmt = $conn->prepare("SELECT first_name, middle_name, last_name, age, contact_info, allergies FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$allergies = $user['allergies'] ? explode(',', $user['allergies']) : [];
$allergy_array = array_map('trim', $allergies);

// Update profile
if(isset($_POST['update_profile'])){
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name']);
    $age = intval($_POST['age'] ?? 0);
    $contact_info = trim($_POST['contact_info'] ?? '');

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, age = ?, contact_info = ? WHERE id = ?");
    $stmt->bind_param("sssisi", $first_name, $middle_name, $last_name, $age, $contact_info, $user_id);
    $stmt->execute();
    $stmt->close();
    
    $message = "Profile updated successfully!";
    
    // Refresh user data
    $stmt = $conn->prepare("SELECT first_name, middle_name, last_name, age, contact_info, allergies FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Add allergy
if(isset($_POST['add'])){
    $new_allergy = trim($_POST['allergy']);
    
    $lower_allergies = array_map('strtolower', $allergy_array);
    
    if($new_allergy != '' && !in_array(strtolower($new_allergy), $lower_allergies)){
        $allergy_array[] = $new_allergy;
        $allergy_str = implode(',', $allergy_array);
        
        $stmt = $conn->prepare("UPDATE users SET allergies = ? WHERE id = ?");
        $stmt->bind_param("si", $allergy_str, $user_id);
        $stmt->execute();
        $stmt->close();
        
        $allergy_message = "$new_allergy added!";
    } else {
        $allergy_message = "$new_allergy is already saved or empty.";
    }
}

// Remove allergy
if(isset($_GET['remove'])){
    $to_remove = trim($_GET['remove']);
    
    $allergy_array = array_filter($allergy_array, function($a) use($to_remove){
        return strtolower($a) !== strtolower($to_remove);
    });
    $allergy_array = array_values($allergy_array);
    
    $allergy_str = implode(',', $allergy_array);
    
    $stmt = $conn->prepare("UPDATE users SET allergies = ? WHERE id = ?");
    $stmt->bind_param("si", $allergy_str, $user_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: client_profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile - Food Order System</title>
<link rel="stylesheet" href="styles.css">
<style>
.profile-container { max-width: 600px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.profile-section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
.profile-section:last-child { border-bottom: none; }
.profile-section h3 { margin-bottom: 15px; color: #333; }
.form-group { margin-bottom: 12px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
.form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
.form-group input:focus { outline: none; border-color: #3A8DFF; }
button { padding: 10px 20px; background: #3A8DFF; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
button:hover { background: #3273d6; }
.allergy-list { list-style: none; }
.allergy-list li { background: #f0f0f0; padding: 8px 12px; margin: 5px 0; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; }
.allergy-list a { color: red; text-decoration: none; font-weight: bold; }
.message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
.success { background: #d4edda; color: #155724; }
.error { background: #f8d7da; color: #721c24; }
.back-link { display: inline-block; margin-bottom: 20px; }
.back-link a { color: #3A8DFF; text-decoration: none; }
.delete-btn { background: #dc3545; }
.delete-btn:hover { background: #c82333; }
</style>
</head>
<body>

<div class="profile-container">
    <a href="client_dashboard.php" class="back-link">&larr; Back to Dashboard</a>

    <?php if(isset($message)) echo "<div class='message success'>$message</div>"; ?>
    <?php if(isset($allergy_message)) echo "<div class='message success'>$allergy_message</div>"; ?>

    <!-- Profile Information Section -->
    <div class="profile-section">
        <h3>Personal Information</h3>
        <form method="post">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Middle Name (Optional)</label>
                <input type="text" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" value="<?php echo htmlspecialchars($user['age'] ?? ''); ?>" min="1" max="120">
            </div>
            <div class="form-group">
                <label>Contact Info (Phone)</label>
                <input type="tel" name="contact_info" value="<?php echo htmlspecialchars($user['contact_info'] ?? ''); ?>">
            </div>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>
    </div>

    <!-- Allergies Section -->
    <div class="profile-section">
        <h3>My Allergies</h3>
        <form method="post">
            <div class="form-group">
                <input type="text" name="allergy" placeholder="Type allergy to add" required>
            </div>
            <button type="submit" name="add">Add Allergy</button>
        </form>

        <h4 style="margin-top: 15px; margin-bottom: 10px;">Saved Allergies:</h4>
        <?php if(count($allergy_array) > 0): ?>
            <ul class="allergy-list">
            <?php foreach($allergy_array as $a): ?>
                <li>
                    <span><?php echo htmlspecialchars($a); ?></span>
                    <a href="?remove=<?php echo urlencode($a); ?>">Remove</a>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="color: #666;">No allergies added yet.</p>
        <?php endif; ?>
    </div>

    <!-- Danger Zone -->
    <div class="profile-section">
        <h3 style="color: #dc3545;">Danger Zone</h3>
        <p>Once you delete your account, it cannot be recovered.</p>
        <a href="?delete_profile=confirm" onclick="return confirm('Are you sure you want to delete your profile? This cannot be undone.')" style="display: inline-block;">
            <button class="delete-btn">Delete My Account</button>
        </a>
    </div>
</div>

</body>
</html>
