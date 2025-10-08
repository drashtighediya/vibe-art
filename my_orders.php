<?php
session_start();
$conn = new mysqli("localhost", "root", "", "art_gallery");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user']['id'];
$cancelMessage = '';
if (isset($_POST['cancel_order'])) {
    $order_id = (int) $_POST['order_id'];
    $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
    if ($stmt) {
        $stmt->bind_param("ii", $order_id, $user_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $cancelMessage = 'success';
            } else {
                $cancelMessage = 'Order not found or already cancelled';
            }
        } else {
            $cancelMessage = 'Error: ' . $stmt->error;
        }
        $stmt->close();
        if ($cancelMessage === 'success') {
            header("Location: my_orders.php?cancelled=1");
            exit();
        }
    } else {
        $cancelMessage = 'Database error: ' . $conn->error;
    }
}
$stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
if (!$stmt) die("Error: " . $conn->error);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
$wishlistCount = 0;
$tableCheck = $conn->query("SHOW TABLES LIKE 'wishlist'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $wishlistCount = $stmt->get_result()->fetch_assoc()['count'];
        $stmt->close();
    }
}
$query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$orders = null;
$hasOrders = false;

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();
    $hasOrders = true;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders - User Dashboard</title>
    <link rel="stylesheet" href="dash.css">
    <style>
        .orders-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .order-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            transform: translateY(-3px);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .order-id {
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
        }
        
        .order-date {
            color: #666;
            font-size: 14px;
        }
        
        .order-content {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        
        .artwork-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .order-details {
            flex: 1;
        }
        
        .artwork-title {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }
        
        .payment-method {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 12px;
        }
        
        .order-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .info-item {
            font-size: 15px;
            color: #555;
        }
        
        .info-label {
            font-weight: 600;
            color: #764ba2;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #ffc3a0, #ffafbd);
            color: #c41e3a;
        }
        
        .status-cancelled {
            background: linear-gradient(135deg, #ffcdd2, #ef5350);
            color: #c62828;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #c8e6c9, #81c784);
            color: #2e7d32;
        }
        
        .price {
            font-size: 26px;
            font-weight: 700;
            color: #667eea;
            margin-top: 10px;
        }
        
        .no-orders {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .no-orders-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .no-orders h3 {
            color: #764ba2;
            margin-bottom: 15px;
        }
        
        .browse-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .browse-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .cancel-btn {
            background: linear-gradient(to right, #ef5350, #e53935);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .cancel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 83, 80, 0.4);
        }
        
        .cancel-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .order-actions {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
        <img src="image/logo.jpg" alt="Art Gallery Logo" style="height: 40px;">
        </div>
        <div class="card blue">Wishlist Items: <span><?php echo $wishlistCount; ?></span></div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($userData['full_name']); ?></span>
        </div>
    </div>

    <div class="sidebar">
        <h2>User Panel</h2>
        <ul>
            <li><a href="user_dashboard.php">Dashboard</a></li>
            <li><a href="my_orders.php">My Orders</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="artworks.php">Browse Artworks</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="center-content">
        <h2 class="title">MY ORDERS</h2>
        
        <?php if (isset($_GET['cancelled']) && $_GET['cancelled'] == 1): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                ✓ Order cancelled successfully!
            </div>
        <?php endif; ?>
        
        <?php if (!empty($cancelMessage) && $cancelMessage !== 'success'): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo htmlspecialchars($cancelMessage); ?>
            </div>
        <?php endif; ?>
        
        <div class="orders-container">
            <?php if ($hasOrders && $orders && $orders->num_rows > 0): ?>
                <?php while ($order = $orders->fetch_assoc()): 
                    $orderStatus = $order['status'] ?? 'pending';
                ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Order #<?php echo htmlspecialchars($order['id']); ?></div>
                                <div class="order-date"><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></div>
                            </div>
                            <span class="status-badge status-<?php echo strtolower($orderStatus); ?>">
                                <?php echo htmlspecialchars(ucfirst($orderStatus)); ?>
                            </span>
                        </div>
                        
                        <div class="order-content">
                            <?php if (!empty($order['image'])): ?>
                                <img src="<?php echo htmlspecialchars($order['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($order['title']); ?>" 
                                     class="artwork-image">
                            <?php else: ?>
                                <div class="artwork-image" style="background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px;">No Image</div>
                            <?php endif; ?>
                            
                            <div class="order-details">
                                <div class="artwork-title"><?php echo htmlspecialchars($order['title']); ?></div>
                                <div class="payment-method">Payment: <?php echo htmlspecialchars($order['payment_method']); ?></div>
                                
                                <div class="order-info">
                                    <div class="info-item">
                                        <span class="info-label">Customer:</span> 
                                        <?php echo htmlspecialchars($order['name']); ?>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Email:</span> 
                                        <?php echo htmlspecialchars($order['email']); ?>
                                    </div>
                                    <div class="info-item" style="grid-column: 1 / -1;">
                                        <span class="info-label">Delivery Address:</span> 
                                        <?php echo htmlspecialchars($order['address']); ?>
                                    </div>
                                </div>
                                
                                <div class="price">₹<?php echo number_format($order['price'], 2); ?></div>
                                
                                <?php if ($orderStatus !== 'cancelled'): ?>
                                    <div class="order-actions">
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" name="cancel_order" class="cancel-btn">Cancel Order</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="order-actions">
                                        <small style="color: #c62828; font-style: italic;">This order has been cancelled</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-orders">
                    <div class="no-orders-icon">📦</div>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders. Start exploring our amazing art collection!</p>
                    <a href="artworks.php" class="browse-btn">Browse Artworks</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>