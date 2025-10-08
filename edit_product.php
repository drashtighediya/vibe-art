<?php
session_start();
$host = 'localhost';
$dbname = 'art_gallery';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';
$messageType = '';
if (!isset($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$productId = $_GET['id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header("Location: manage_products.php");
        exit();
    }
} catch(PDOException $e) {
    die("Error fetching product: " . $e->getMessage());
}
try {
    $artistStmt = $pdo->query("SELECT * FROM artists ORDER BY artist_name");
    $artists = $artistStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $artists = [];
}
try {
    $typeStmt = $pdo->query("SELECT * FROM arttype ORDER BY type_name");
    $artTypes = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $artTypes = [];
}
try {
    $mediumStmt = $pdo->query("SELECT * FROM artmedium ORDER BY medium_name");
    $artMediums = $mediumStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $artMediums = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $description = $_POST['description'] ?? '';
    $artist_id = !empty($_POST['artist_id']) ? $_POST['artist_id'] : $product['artist_id'];
    $type_id = !empty($_POST['type_id']) ? $_POST['type_id'] : $product['type_id'];
    $medium_id = !empty($_POST['medium_id']) ? $_POST['medium_id'] : $product['medium_id'];
    $imageName = $product['image'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($imageFileType, $allowedTypes)) {
            if (!empty($product['image']) && file_exists($uploadDir . $product['image'])) {
                unlink($uploadDir . $product['image']);
            }
            $imageName = uniqid() . '_' . time() . '.' . $imageFileType;
            $targetFile = $uploadDir . $imageName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            } else {
                $message = "Error uploading image.";
                $messageType = "error";
                $imageName = $product['image']; 
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $messageType = "error";
        }
    }
    if (empty($message)) {
        try {
            $sql = "UPDATE product SET title = ?, price = ?, description = ?, image = ?";
            $params = [$title, $price, $description, $imageName];
            
            if ($artist_id !== null && $artist_id !== '') {
                $sql .= ", artist_id = ?";
                $params[] = $artist_id;
            }
            
            if ($type_id !== null && $type_id !== '') {
                $sql .= ", type_id = ?";
                $params[] = $type_id;
            }
            
            if ($medium_id !== null && $medium_id !== '') {
                $sql .= ", medium_id = ?";
                $params[] = $medium_id;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $productId;
            
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute($params)) {
                $message = "Product updated successfully!";
                $messageType = "success";
                $stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
                $stmt->execute([$productId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $message = "Error updating product.";
                $messageType = "error";
            }
        } catch(PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Art Gallery</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 40px;
        }
        
        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 30px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        input[type="file"] {
            padding: 10px;
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            width: 100%;
        }
        
        .current-image {
            margin-top: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }
        
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Product</h1>
            <p>Update product information</p>
        </div>
        
        <div class="content">
            <a href="manage_products.php" class="back-link">← Back to Manage Products</a>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Product Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (₹) <span class="required">*</span></label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
                
                <?php if (count($artists) > 0): ?>
                <div class="form-group">
                    <label for="artist_id">Artist</label>
                    <select id="artist_id" name="artist_id">
                        <option value="">-- Select Artist --</option>
                        <?php foreach ($artists as $artist): ?>
                            <option value="<?php echo $artist['id']; ?>" 
                                <?php echo ($product['artist_id'] == $artist['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($artist['artist_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <?php if (count($artTypes) > 0): ?>
                <div class="form-group">
                    <label for="type_id">Art Type</label>
                    <select id="type_id" name="type_id">
                        <option value="">-- Select Type --</option>
                        <?php foreach ($artTypes as $type): ?>
                            <option value="<?php echo $type['id']; ?>" 
                                <?php echo ($product['type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <?php if (count($artMediums) > 0): ?>
                <div class="form-group">
                    <label for="medium_id">Art Medium</label>
                    <select id="medium_id" name="medium_id">
                        <option value="">-- Select Medium --</option>
                        <?php foreach ($artMediums as $medium): ?>
                            <option value="<?php echo $medium['id']; ?>" 
                                <?php echo ($product['medium_id'] == $medium['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($medium['medium_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    
                    <?php if (!empty($product['image'])): ?>
                    <div class="current-image">
                        <strong>Current Image:</strong>
                        <br>
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <p style="margin-top: 10px; color: #666; font-size: 14px;">
                            Leave empty to keep current image, or upload a new one to replace it.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Update Product</button>
                    <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>