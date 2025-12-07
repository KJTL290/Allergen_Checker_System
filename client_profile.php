<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    die("Not logged in");
}

$user_id = $_SESSION['user_id'];

// Fetch existing allergies
$result = $conn->query("SELECT allergies FROM users WHERE id='$user_id'");
$row = $result->fetch_assoc();
$allergies = $row['allergies'] ?? '';
$allergy_array = $allergies ? explode(',', $allergies) : [];

// ADD allergy
if(isset($_POST['add'])){
    $new_allergy = trim($_POST['allergy']);

    $lower_allergies = array_map('strtolower', $allergy_array);

    if($new_allergy != '' && !in_array(strtolower($new_allergy), $lower_allergies)){
        $allergy_array[] = $new_allergy;
        $allergy_str = implode(',', $allergy_array);
        $conn->query("UPDATE users SET allergies='$allergy_str' WHERE id='$user_id'");
        $message = "$new_allergy added!";
    } else {
        $message = "$new_allergy is already saved or empty.";
    }
}

// REMOVE allergy
if(isset($_GET['remove'])){
    $to_remove = $_GET['remove'];

    // Filter out the removed allergy (case-insensitive)
    $allergy_array = array_filter($allergy_array, function($a) use($to_remove){
        return strtolower($a) !== strtolower($to_remove);
    });

    $allergy_str = implode(',', $allergy_array);
    $conn->query("UPDATE users SET allergies='$allergy_str' WHERE id='$user_id'");
    header("Location: client_profile.php"); // reload page to update list
    exit;
}
?>

<h2>My Allergies</h2>
<?php if(isset($message)) echo "<p>$message</p>"; ?>

<form method="post">
    <input type="text" name="allergy" placeholder="Type allergy">
    <button name="add">Add Allergy</button>
</form>

<h3>Saved Allergies:</h3>
<ul>
<?php
foreach($allergy_array as $a){
    echo "<li>$a <a href='?remove=".urlencode($a)."' style='color:red;'>[Remove]</a></li>";
}
?>
</ul>

<a href="client_dashboard.php" style="color:blue;">&larr; Back</a>
