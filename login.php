<?php include "db.php"; ?>
<?php
session_start();

if(isset($_POST['register'])){
    $u = trim($_POST['username']);
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users(username, password) VALUES(?, ?)");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $stmt->close();
}

if(isset($_POST['login'])){
    $u = trim($_POST['username']);
    $p = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        if(password_verify($p, $row['password'])){
            $_SESSION['id']   = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_id'] = $row['id'];

            if($row['role'] == 'admin'){
                header("location: admin_dashboard.php");
            } elseif($row['role'] == 'staff'){
                header("location: staff_dashboard.php");
            } else {
                header("location: client_dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Username not found!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Allergen Checker</title>
<link rel="stylesheet" href="styles.css">
</head>
<body class="login-page">

<div class="login-box">
    <h2>🍔 Allergen Checker</h2>
    <p style="margin-bottom: 30px; color: #666;">Food Ordering & Allergy Management System</p>

    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <a href="client_register.php">Don't have an account? Create one here</a>
</div>

</body>
</html>
