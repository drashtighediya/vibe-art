<?php
session_start();
require_once 'config.php';

$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
$products = [];
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
$wishlist_ids = [];
if (isset($_SESSION['user'])) {
    $user_id = (int)$_SESSION['user']['id'];
    
    $tableCheck = $conn->query("SHOW TABLES LIKE 'wishlist'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $wishlist_sql = "SELECT product_id FROM wishlist WHERE user_id = ?";
        $stmt = $conn->prepare($wishlist_sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $wishlist_result = $stmt->get_result();
            while ($row = $wishlist_result->fetch_assoc()) {
                $wishlist_ids[] = $row['product_id'];
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - VibeArt Gallery</title>
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
            max-width: 1400px;
        }
        h1 {
            color: #764ba2;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5rem;
        }
        .product-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(118, 75, 162, 0.3);
            border-color: #764ba2;
        }
        .product-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 2px solid #f0f0f0;
        }
        .product-body {
            padding: 20px;
        }
        .product-title {
            color: #667eea;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .product-price {
            color: #764ba2;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .btn-buy {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
        }
        .btn-buy:hover {
            background: linear-gradient(to right, #764ba2, #667eea);
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
        .wishlist-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 10;
            font-size: 1.2rem;
        }
        .wishlist-icon.active {
            background: #764ba2;
            color: white;
        }
        .alert-success {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(400px); }
            to { transform: translateX(0); }
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <?php if (isset($_SESSION['user'])): ?>
        <a href="user_dashboard.php" class="back-btn">← Back to Dashboard</a>
    <?php else: ?>
        <a href="index.php" class="back-btn">← Back to Home</a>
    <?php endif; ?>
    
    <h1>🎨 Art Gallery Shop</h1>

    <?php if (isset($_GET['added']) && $_GET['added'] == 'wishlist'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Added to wishlist! 💜
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['added']) && $_GET['added'] == 'cart'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Added to cart! 🛒
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($products)): ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="product-card">
                        <?php if (isset($_SESSION['user'])): ?>
                            <div class="wishlist-icon <?= in_array($product['id'], $wishlist_ids) ? 'active' : '' ?>" 
                                 onclick="toggleWishlist(<?= $product['id'] ?>, this)">
                                ❤
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div class="product-img" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                🎨
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-body">
                            <h5 class="product-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="product-price">₹<?= number_format($product['price'], 2) ?></p>
                            
                            <?php if (!empty($product['description'])): ?>
                                <p class="text-muted small mb-3"><?= htmlspecialchars(substr($product['description'], 0, 60)) ?>...</p>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2 flex-column">
                                <a href="product_details.php?id=<?= (int)$product['id'] ?>" class="btn btn-sm" style="background: #f0f0f0; color: #764ba2;">View Details</a>
                                
                                <?php if (isset($_SESSION['user'])): ?>
                                    <form action="add_to_cart.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-buy w-100">🛒 Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-buy">🛒 Login to Buy</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center" style="padding: 60px;">
            <h3>No artworks available yet! 🎨</h3>
            <p>Check back soon for amazing art pieces.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleWishlist(productId, element) {
    fetch('toggle_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            element.classList.toggle('active');
            
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show';
            notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 1000;';
            notification.innerHTML = data.message + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            document.body.appendChild(notification);
            
            setTimeout(() => notification.remove(), 3000);
        } else {
            alert(data.message || 'Error updating wishlist');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update wishlist. Please try again.');
    });
}
</script>
</body>
</html>