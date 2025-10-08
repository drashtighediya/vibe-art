<?php
session_start();
require_once 'config.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user']['id'];

// Create wishlist table if it doesn't exist
$tableCheck = $conn->query("SHOW TABLES LIKE 'wishlist'");
if (!$tableCheck || $tableCheck->num_rows == 0) {
    $conn->query("CREATE TABLE IF NOT EXISTS wishlist (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        artwork_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_wishlist (user_id, artwork_id)
    )");
}

// Check if artworks table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'artworks'");
if (!$tableCheck || $tableCheck->num_rows == 0) {
    $rows = [];
} else {
    // Fetch wishlist artworks
    $sql = "SELECT a.* 
            FROM artworks a 
            INNER JOIN wishlist w ON a.id = w.artwork_id 
            WHERE w.user_id = ?
            ORDER BY w.created_at DESC";

    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        die("Query failed: " . $stmt->error);
    }

    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Art Gallery</title>
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
        .artwork-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }
        .artwork-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(118, 75, 162, 0.3);
            border-color: #764ba2;
        }
        .artwork-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 2px solid #f0f0f0;
        }
        .artwork-body {
            padding: 20px;
        }
        .artwork-title {
            color: #667eea;
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }
        .artwork-artist {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }
        .artwork-price {
            color: #764ba2;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .btn-remove {
            background: linear-gradient(to right, #e04a4a, #764ba2);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-remove:hover {
            background: linear-gradient(to right, #764ba2, #e04a4a);
            transform: scale(1.05);
            color: white;
        }
        .btn-view {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-view:hover {
            background: linear-gradient(to right, #764ba2, #667eea);
            color: white;
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
            transform: translateX(-5px);
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
            border-radius: 12px;
        }
        .empty-state h3 {
            color: #764ba2;
            font-size: 2rem;
            margin-bottom: 15px;
        }
        .empty-state p {
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 25px;
        }
        .shop-btn {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            padding: 15px 40px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .shop-btn:hover {
            background: linear-gradient(to right, #764ba2, #667eea);
            color: white;
            transform: scale(1.05);
        }
        .wishlist-count {
            background: #764ba2;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <a href="user_dashboard.php" class="back-btn">← Back to Dashboard</a>
    <h1>💜 My Wishlist</h1>

    <?php if (!empty($rows)): ?>
        <div class="text-center mb-4">
            <span class="wishlist-count"><?= count($rows) ?> Artwork<?= count($rows) > 1 ? 's' : '' ?> in Wishlist</span>
        </div>
        
        <div class="row g-4">
            <?php foreach ($rows as $row): ?>
                <div class="col-md-4">
                    <div class="artwork-card">
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?= htmlspecialchars($row['image']) ?>" class="artwork-img" alt="<?= htmlspecialchars($row['title'] ?? $row['name'] ?? 'Artwork') ?>">
                        <?php else: ?>
                            <div class="artwork-img" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                🎨
                            </div>
                        <?php endif; ?>
                        
                        <div class="artwork-body">
                            <h5 class="artwork-title"><?= htmlspecialchars($row['title'] ?? $row['name'] ?? 'Untitled') ?></h5>
                            
                            <?php if (!empty($row['artist_name'])): ?>
                                <p class="artwork-artist">by <?= htmlspecialchars($row['artist_name']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (isset($row['price'])): ?>
                                <p class="artwork-price">₹<?= number_format($row['price'], 2) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($row['description'])): ?>
                                <p class="text-muted small mb-3"><?= htmlspecialchars(substr($row['description'], 0, 80)) ?>...</p>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2">
                                <a href="artwork_details.php?id=<?= (int)$row['id'] ?>" class="btn btn-view flex-grow-1">View Details</a>
                                <a href="remove_wishlist.php?artwork_id=<?= (int)$row['id'] ?>" class="btn btn-remove" onclick="return confirm('Remove this artwork from wishlist?');">Remove</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3>Your wishlist is empty! 💔</h3>
            <p>Start adding your favorite artworks to your wishlist and never lose track of what you love.</p>
            <a href="view_artworks.php" class="shop-btn">🎨 Browse Artworks</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>