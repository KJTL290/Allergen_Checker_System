<?php include "db.php"; ?>
<a href="admin_dashboard.php" style="color:blue; display:inline-block; margin-bottom:15px;">&larr; Back</a>

<h2>Order Queue</h2>
<?php
$q=$conn->query("SELECT o.id, f.name, o.status 
                 FROM orders o JOIN food f ON o.food_id=f.id
                 ORDER BY o.created_at ASC");
while($r=$q->fetch_assoc()){
  echo "Order #{$r['id']} - {$r['name']} ({$r['status']})<br>";
}
?>
