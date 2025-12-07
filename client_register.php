<?php include "db.php"; ?>

<?php
if(isset($_POST['register'])){
    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // client only
    $sql = "INSERT INTO users(username, password, role) 
            VALUES('$u', '$p', 'client')";
    $conn->query($sql);

    header("location: login.php");
}
?>

<h2>Client Registration</h2>
<form method="post">
  Username <input name="username" required><br>
  Password <input type="password" name="password" required><br>
  <button name="register">Register</button>
</form>
<a href="login.php">Back to Login</a>
