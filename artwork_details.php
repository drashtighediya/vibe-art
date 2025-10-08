<?php
session_start();
$conn = new mysqli("localhost", "root", "", "art_gallery");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
$userData = null;
$wishlistCount = 0;
if ($user_id) {
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();
    }
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
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_wishlist']) && $user_id) {
    $artwork_id = (int)$_POST['artwork_id'];
    
    $tableCheck = $conn->query("SHOW TABLES LIKE 'wishlist'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND artwork_id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $artwork_id);
            $stmt->execute();
            $exists = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            
            if ($exists) {
                $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND artwork_id = ?");
                if ($stmt) {
                    $stmt->bind_param("ii", $user_id, $artwork_id);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO wishlist (user_id, artwork_id, added_at) VALUES (?, ?, NOW())");
                if ($stmt) {
                    $stmt->bind_param("ii", $user_id, $artwork_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
    $redirect_url = "artworks.php";
    if (!empty($_SERVER['QUERY_STRING'])) {
        $redirect_url .= "?" . $_SERVER['QUERY_STRING'];
    }
    header("Location: " . $redirect_url);
    exit();
}

$columnsResult = $conn->query("SHOW COLUMNS FROM artworks");
$availableColumns = [];
while ($col = $columnsResult->fetch_assoc()) {
    $availableColumns[] = $col['Field'];
}
$hasArtistId = in_array('artist_id', $availableColumns);
$hasCategoryId = in_array('category_id', $availableColumns);
$hasTitle = in_array('title', $availableColumns);
$hasDescription = in_array('description', $availableColumns);
$hasPrice = in_array('price', $availableColumns);
$hasImagePath = in_array('image_path', $availableColumns);
$artistTableExists = false;
$categoryTableExists = false;

$tableCheck = $conn->query("SHOW TABLES LIKE 'artists'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $artistTableExists = true;
}

$tableCheck = $conn->query("SHOW TABLES LIKE 'categories'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $categoryTableExists = true;
}

$wishlistTableExists = false;
$tableCheck = $conn->query("SHOW TABLES LIKE 'wishlist'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $wishlistTableExists = true;
}
$category = isset($_GET['category']) && $_GET['category'] !== '' ? trim($_GET['category']) : '';
$search = isset($_GET['search']) && $_GET['search'] !== '' ? trim($_GET['search']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;

$query = "SELECT a.id";
if ($hasTitle) $query .= ", a.title";
if ($hasDescription) $query .= ", a.description";
if ($hasPrice) $query .= ", a.price";
if ($hasImagePath) $query .= ", a.image_path";
if ($hasArtistId) $query .= ", a.artist_id";
if ($hasCategoryId) $query .= ", a.category_id";
if ($artistTableExists && $hasArtistId) {
    $query .= ", ar.artist_name";
} else {
    $query .= ", 'Unknown Artist' as artist_name";
}

if ($categoryTableExists && $hasCategoryId) {
    $query .= ", c.category_name";
} else {
    $query .= ", NULL as category_name";
}

if ($user_id && $wishlistTableExists) {
    $query .= ", (SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND artwork_id = a.id) as in_wishlist";
} else {
    $query .= ", 0 as in_wishlist";
}

$query .= " FROM artworks a";

if ($artistTableExists && $hasArtistId) {
    $query .= " LEFT JOIN artists ar ON a.artist_id = ar.id";
}
if ($categoryTableExists && $hasCategoryId) {
    $query .= " LEFT JOIN categories c ON a.category_id = c.id";
}

$query .= " WHERE 1=1";

$params = [];
$types = "";

if ($user_id && $wishlistTableExists) {
    $params[] = $user_id;
    $types .= "i";
}
if ($category && $categoryTableExists && $hasCategoryId) {
    $query .= " AND c.category_name = ?";
    $params[] = $category;
    $types .= "s";
}
if ($search) {
    $searchConditions = [];
    $searchTerm = "%$search%";
    
    if ($hasTitle) {
        $searchConditions[] = "a.title LIKE ?";
        $params[] = $searchTerm;
        $types .= "s";
    }
    
    if ($hasDescription) {
        $searchConditions[] = "a.description LIKE ?";
        $params[] = $searchTerm;
        $types .= "s";
    }
    
    if ($artistTableExists && $hasArtistId) {
        $searchConditions[] = "ar.artist_name LIKE ?";
        $params[] = $searchTerm;
        $types .= "s";
    }
    
    if (!empty($searchConditions)) {
        $query .= " AND (" . implode(" OR ", $searchConditions) . ")";
    }
}
if ($hasPrice && ($min_price !== null || $max_price !== null)) {
    if ($min_price !== null && $max_price !== null) {
        $query .= " AND a.price BETWEEN ? AND ?";
        $params[] = $min_price;
        $params[] = $max_price;
        $types .= "dd";
    } elseif ($min_price !== null) {
        $query .= " AND a.price >= ?";
        $params[] = $min_price;
        $types .= "d";
    } elseif ($max_price !== null) {
        $query .= " AND a.price <= ?";
        $params[] = $max_price;
        $types .= "d";
    }
}

$query .= " ORDER BY a.id DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$artworks = $stmt->get_result();

$stmt->close();

$categories = null;
if ($categoryTableExists) {
    $categories = $conn->query("SELECT DISTINCT category_name FROM categories ORDER BY category_name");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Browse Artworks - VibeArt Gallery</title>
    <link rel="stylesheet" href="dash.css">
    <style>
        .artworks-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-label {
            font-weight: 600;
            color: #764ba2;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .filter-input, .filter-select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .filter-btn {
            padding: 10px 25px;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .artworks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .artwork-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .artwork-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .artwork-image-container {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .artwork-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .artwork-card:hover .artwork-image {
            transform: scale(1.05);
        }
        
        .wishlist-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .wishlist-btn:hover {
            transform: scale(1.1);
        }
        
        .wishlist-btn.active {
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            color: white;
        }
        
        .category-badge {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .artwork-info {
            padding: 20px;
        }
        
        .artwork-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .artwork-artist {
            color: #667eea;
            font-size: 15px;
            margin-bottom: 12px;
            font-weight: 500;
        }
        
        .artwork-description {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .artwork-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 2px solid #f0f0f0;
        }
        
        .artwork-price {
            font-size: 24px;
            font-weight: 700;
            color: #764ba2;
        }
        
        .view-btn {
            padding: 10px 20px;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .view-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .no-artworks {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .no-artworks-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
        <img src="image/logo.jpg" alt="Art Gallery Logo" style="height: 40px;">
        </div>
        <?php if ($user_id): ?>
            <div class="card blue">Wishlist Items: <span><?php echo $wishlistCount; ?></span></div>
            <div class="user-info">
                <span><?php echo htmlspecialchars($userData['full_name']); ?></span>
            </div>
        <?php else: ?>
            <div class="user-info">
                <a href="login.php" style="color: white; text-decoration: none;">Login</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($user_id): ?>
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
    <?php endif; ?>

    <div class="center-content" style="<?php echo !$user_id ? 'margin-left: 0;' : ''; ?>">
        <h2 class="title">BROWSE ARTWORKS</h2>
        
        <div class="artworks-container">
            <div class="filters-section">
                <form method="GET" action="artworks.php" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="text" name="search" class="filter-input" 
                               placeholder="Search artworks..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <?php if ($categoryTableExists && $categories && $categories->num_rows > 0): ?>
                    <div class="filter-group">
                        <label class="filter-label">Category</label>
                        <select name="category" class="filter-select">
                            <option value="">All Categories</option>
                            <?php 
                            $categories->data_seek(0); 
                            while ($cat = $categories->fetch_assoc()): 
                            ?>
                                <option value="<?php echo htmlspecialchars($cat['category_name']); ?>"
                                        <?php echo ($category === $cat['category_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($hasPrice): ?>
                    <div class="filter-group">
                        <label class="filter-label">Min Price (₹)</label>
                        <input type="number" name="min_price" class="filter-input" 
                               placeholder="0" step="0.01" min="0"
                               value="<?php echo $min_price !== null ? $min_price : ''; ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Max Price (₹)</label>
                        <input type="number" name="max_price" class="filter-input" 
                               placeholder="No limit" step="0.01" min="0"
                               value="<?php echo $max_price !== null ? $max_price : ''; ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="filter-group">
                        <button type="submit" class="filter-btn">Apply Filters</button>
                    </div>
                    
                    <div class="filter-group">
                        <a href="artworks.php" class="filter-btn" style="background: #6c757d; text-decoration: none; text-align: center; display: block;">Clear Filters</a>
                    </div>
                </form>
            </div>
            
            <?php if ($artworks->num_rows > 0): ?>
                <div class="artworks-grid">
                    <?php while ($artwork = $artworks->fetch_assoc()): ?>
                        <div class="artwork-card">
                            <div class="artwork-image-container">
                                <?php if ($hasImagePath && !empty($artwork['image_path'])): ?>
                                    <?php 
                                    $imagePath = $artwork['image_path'];
                                    if (!preg_match('/^(http|https|data):/', $imagePath)) {
                                        if (strpos($imagePath, 'uploads/') === 0 || strpos($imagePath, './uploads/') === 0) {
                                            $imagePath = $imagePath;
                                        } elseif (!preg_match('/^(\/|\.\.?\/)/', $imagePath)) {
                                            $imagePath = 'uploads/' . $imagePath;
                                        }
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                         alt="<?php echo $hasTitle ? htmlspecialchars($artwork['title']) : 'Artwork'; ?>" 
                                         class="artwork-image"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'artwork-image\' style=\'display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; flex-direction: column;\'><div style=\'font-size: 40px; margin-bottom: 10px;\'>🖼️</div><div>Image Not Found</div></div>';">
                                <?php else: ?>
                                    <div class="artwork-image" style="display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; flex-direction: column;">
                                        <div style="font-size: 40px; margin-bottom: 10px;">🎨</div>
                                        <div>No Image Available</div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($user_id && $wishlistTableExists): ?>
                                    <form method="POST" action="?<?php echo http_build_query($_GET); ?>" style="display: inline;">
                                        <input type="hidden" name="artwork_id" value="<?php echo $artwork['id']; ?>">
                                        <button type="submit" name="toggle_wishlist" 
                                                class="wishlist-btn <?php echo (!empty($artwork['in_wishlist']) && $artwork['in_wishlist'] > 0) ? 'active' : ''; ?>">
                                            <?php echo (!empty($artwork['in_wishlist']) && $artwork['in_wishlist'] > 0) ? '❤️' : '🤍'; ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if (!empty($artwork['category_name'])): ?>
                                    <span class="category-badge"><?php echo htmlspecialchars($artwork['category_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="artwork-info">
                                <?php if ($hasTitle): ?>
                                    <div class="artwork-title"><?php echo htmlspecialchars($artwork['title']); ?></div>
                                <?php endif; ?>
                                
                                <div class="artwork-artist">by <?php echo htmlspecialchars($artwork['artist_name']); ?></div>
                                
                                <?php if ($hasDescription && !empty($artwork['description'])): ?>
                                    <div class="artwork-description"><?php echo htmlspecialchars($artwork['description']); ?></div>
                                <?php endif; ?>
                                
                                <div class="artwork-footer">
                                    <?php if ($hasPrice): ?>
                                        <div class="artwork-price">₹<?php echo number_format($artwork['price'], 2); ?></div>
                                    <?php endif; ?>
                                    <a href="artwork_details.php?id=<?php echo $artwork['id']; ?>" class="view-btn">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-artworks">
                    <div class="no-artworks-icon">🎨</div>
                    <h3>No Artworks Found</h3>
                    <p>Try adjusting your filters or search criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>