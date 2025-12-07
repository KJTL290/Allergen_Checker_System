<?php include "db.php"; ?>
<?php
session_start();

if(isset($_POST['register'])){
    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users(username,password) VALUES('$u','$p')";
    $conn->query($sql);
}

if(isset($_POST['login'])){
    $u = $_POST['username'];
    $p = $_POST['password'];

    $q = $conn->query("SELECT * FROM users WHERE username='$u'");
    if($q->num_rows > 0){
        $row = $q->fetch_assoc();
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Food Order System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width:400px; margin:auto; padding-top:50px;">
    <div class="login-box">
        <h2>Food Order System Login</h2>

        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button name="login">Login</button>
        </form>

        <a href="client_register.php">Create Client Account</a>
    </div>
</div>

</body>
</html>
