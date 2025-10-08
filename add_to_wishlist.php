<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['product_id'])) {
    header("Location: shop.php");
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
$product_id = (int)$_POST['product_id'];

$tableCheck = $conn->query("SHOW TABLES LIKE 'cart'");
if (!$tableCheck || $tableCheck->num_rows == 0) {
    $conn->query("CREATE TABLE IF NOT EXISTS cart (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_cart (user_id, product_id)
    )");
}

$check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + 1;
    
    $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
    $stmt->execute();
} else {
    $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

$stmt->close();
$conn->close();

header("Location: cart.php?added=success");
exit();
?>