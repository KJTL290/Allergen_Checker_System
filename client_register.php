<?php include "db.php"; ?>
<?php
if(isset($_POST['register'])){
    $u = trim($_POST['username']);
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fn = trim($_POST['first_name']);
    $mn = trim($_POST['middle_name'] ?? '');
    $ln = trim($_POST['last_name']);
    $age = intval($_POST['age'] ?? 0);
    $contact = trim($_POST['contact_info'] ?? '');

    $stmt = $conn->prepare("INSERT INTO users(username, password, role, first_name, middle_name, last_name, age, contact_info) 
                           VALUES(?, ?, 'client', ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $u, $p, $fn, $mn, $ln, $age, $contact);
    if($stmt->execute()){
        $success = "Account created! Redirecting to login...";
        header("refresh:2; url=login.php");
    } else {
        $error = "Registration failed. Username may already exist.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Registration - Food Order System</title>
<link rel="stylesheet" href="styles.css">
<style>
.registration-form { max-width: 500px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.registration-form h2 { text-align: center; margin-bottom: 30px; }
.registration-form input, .registration-form textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
.registration-form button { width: 100%; padding: 12px; background: #3A8DFF; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
.registration-form button:hover { background: #3273d6; }
.registration-form a { display: block; text-align: center; margin-top: 15px; color: #3A8DFF; text-decoration: none; }
.success { color: green; text-align: center; font-weight: bold; }
.error { color: red; text-align: center; font-weight: bold; }
</style>
</head>
<body>

<div class="registration-form">
    <h2>Create Client Account</h2>
    
    <?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="middle_name" placeholder="Middle Name (Optional)">
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="number" name="age" placeholder="Age" min="1" max="120">
        <input type="tel" name="contact_info" placeholder="Contact Info (Phone)">
        <button type="submit" name="register">Register</button>
    </form>

    <a href="login.php">Already have an account? Login here</a>
</div>

</body>
</html>
