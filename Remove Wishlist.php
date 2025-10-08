<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
$product_id = (int)$_POST['product_id'];

$check_sql = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist! 💔', 'action' => 'removed']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing from wishlist']);
    }
} else {
    $insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Added to wishlist! 💜', 'action' => 'added']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding to wishlist']);
    }
}

$stmt->close();
$conn->close();
?>