<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user']['id'];

if (!isset($_GET['product_id'])) {
    header("Location: wishlist.php");
    exit();
}

$product_id = (int) $_GET['product_id'];
$sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Item removed from wishlist successfully!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Failed to remove item from wishlist.";
    $_SESSION['message_type'] = "danger";
}

$stmt->close();
$conn->close();

header("Location: wishlist.php");
exit();
?>