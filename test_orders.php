<?php
session_start();
$conn = new mysqli("localhost", "root", "", "art_gallery");

if (!isset($_SESSION['user'])) {
    die("Please login first");
}

$user_id = (int) $_SESSION['user']['id'];

echo "<h2>Debug: Your Orders</h2>";
echo "<p>User ID: " . $user_id . "</p>";

$result = $conn->query("SELECT * FROM orders WHERE user_id = $user_id");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Created</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No orders found</p>";
}
echo "<h3>Orders Table Structure:</h3>";
$structure = $conn->query("DESCRIBE orders");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
while ($row = $structure->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>
<br><br>
<a href="my_orders.php">Go to My Orders</a>