    <?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
$sql = "SELECT p.*, c.quantity, c.id as cart_id 
        FROM products p 
        INNER JOIN cart c ON p.id = c.product_id 
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - VibeArt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: Arial, sans-serif;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 1200px;
        }
        h1 {
            color: #764ba2;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5rem;
        }
        .cart-item {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .cart-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }
        .item-details {
            flex: 1;
        }
        .item-title {
            color: #667eea;
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }
        .item-price {
            color: #764ba2;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-btn {
            background: #764ba2;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 5px;
            cursor: pointer;
        }
        .quantity-btn:hover {
            background: #667eea;
        }
        .btn-remove {
            background: #e04a4a;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
        }
        .btn-remove:hover {
            background: #c03a3a;
        }
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
        }
        .total-amount {
            font-size: 2rem;
            font-weight: bold;
        }
        .btn-checkout {
            background: white;
            color: #764ba2;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .btn-checkout:hover {
            background: #f0f0f0;
            transform: scale(1.05);
        }
        .back-btn {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .back-btn:hover {
            background: linear-gradient(to right, #764ba2, #667eea);
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <a href="shop.php" class="back-btn">← Continue Shopping</a>
    <h1>🛒 Shopping Cart</h1>

    <?php if (isset($_GET['added']) && $_GET['added'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Product added to cart successfully! 🎉
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($cart_items)): ?>
        <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <?php if (!empty($item['image'])): ?>
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <?php else: ?>
                    <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; border-radius: 8px;">
                        🎨
                    </div>
                <?php endif; ?>
                
                <div class="item-details">
                    <h5 class="item-title"><?= htmlspecialchars($item['name']) ?></h5>
                    <p class="item-price">₹<?= number_format($item['price'], 2) ?> each</p>
                    
                    <div class="quantity-control">
                        <span>Quantity:</span>
                        <form action="update_cart.php" method="POST" style="display: inline;">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit" class="quantity-btn">-</button>
                        </form>
                        
                        <span style="font-weight: bold; font-size: 1.1rem;"><?= $item['quantity'] ?></span>
                        
                        <form action="update_cart.php" method="POST" style="display: inline;">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <input type="hidden" name="action" value="increase">
                            <button type="submit" class="quantity-btn">+</button>
                        </form>
                    </div>
                    
                    <p class="mt-2" style="color: #764ba2; font-weight: bold;">
                        Subtotal: ₹<?= number_format($item['price'] * $item['quantity'], 2) ?>
                    </p>
                </div>
                
                <form action="remove_from_cart.php" method="POST">
                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                    <button type="submit" class="btn btn-remove" onclick="return confirm('Remove this item?');">Remove</button>
                </form>
            </div>
        <?php endforeach; ?>

        <div class="total-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3>Order Total</h3>
                    <p class="total-amount">₹<?= number_format($total, 2) ?></p>
                    <p class="mb-0">Total items: <?= count($cart_items) ?></p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout →</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center" style="padding: 60px;">
            <h3>Your cart is empty! 🛒</h3>
            <p>Start adding some amazing artworks to your cart.</p>
            <a href="shop.php" class="btn" style="background: linear-gradient(to right, #667eea, #764ba2); color: white; padding: 12px 30px; border-radius: 8px;">Browse Shop</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>