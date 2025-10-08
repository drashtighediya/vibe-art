<?php
session_start();
require_once "config.php";

$success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = null;
    if (isset($_SESSION['user']['id'])) {
        $user_id = $_SESSION['user']['id'];
    } elseif (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    
    if ($user_id === null) {
        header("Location: login.php");
        exit();
    }
    $title   = $_POST['title'] ?? '';
    $price   = $_POST['price'] ?? 0;
    $image   = $_POST['image'] ?? '';
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $payment = $_POST['payment'] ?? '';
    $sql = "INSERT INTO orders (user_id, title, price, image, name, email, address, payment_method, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("isdsssss", $user_id, $title, $price, $image, $name, $email, $address, $payment);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error_message = "Execute failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Prepare failed: " . $conn->error;
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #4a00e0, #8e2de2);
            font-family: 'Poppins', sans-serif;
        }
        .status-card {
            max-width: 550px;
            margin: 80px auto;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 6px 24px rgba(0,0,0,0.15);
        }
        .status-card img {
            max-width: 150px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .status-card h2 {
            font-weight: 700;
            margin-bottom: 15px;
        }
        .status-card p {
            color: #555;
            margin-bottom: 12px;
        }
        .btn-gradient {
            background: linear-gradient(to right, #4a00e0, #8e2de2);
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 10px 24px;
            border-radius: 30px;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-gradient:hover {
            opacity: 0.9;
            color: #fff;
        }
        .error-details {
            background: #fee;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 14px;
            color: #c00;
        }
    </style>
</head>
<body>
    <div class="status-card">
        <?php if ($success): ?>
            <?php if (!empty($image)): ?>
                <img src="<?= htmlspecialchars($image) ?>" alt="Artwork">
            <?php endif; ?>
            <h2 style="color:#4a00e0;">Order Successful!</h2>
            <p>Thank you <strong><?= htmlspecialchars($name ?? '') ?></strong>, your order for</p>
            <p><strong><?= htmlspecialchars($title ?? '') ?></strong> has been placed successfully 🎉</p>
            <p><strong>Amount:</strong> ₹<?= htmlspecialchars($price ?? 0) ?></p>
            <a href="my_orders.php" class="btn btn-gradient mt-3">View My Orders</a>
        <?php else: ?>
            <h2 style="color:#e04a4a;">Order Failed</h2>
            <p>Sorry, there was a problem processing your order.</p>
            <?php if (!empty($error_message)): ?>
                <div class="error-details">
                    <strong>Error Details:</strong><br>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            <a href="artworks.php" class="btn btn-gradient mt-3">Browse Artworks</a>
        <?php endif; ?>
    </div>
</body>
</html>