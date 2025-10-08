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
$artistsStmt = $pdo->query("SELECT id, name FROM artists ORDER BY name ASC");
$artists = $artistsStmt->fetchAll(PDO::FETCH_ASSOC);
$artTypesStmt = $pdo->query("SELECT id, type_name FROM arttype ORDER BY type_name ASC");
$artTypes = $artTypesStmt->fetchAll(PDO::FETCH_ASSOC);
$artMediums = [];
try {
    $artMediumsStmt = $pdo->query("SELECT id, name FROM artmedium ORDER BY name ASC");
    $artMediums = $artMediumsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $artMediums = [];
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    
    $title = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $artist_id = $_POST['artist_id'];
    $type_id = $_POST['type_id'];
    $medium_id = (!empty($_POST['medium_id']) && $_POST['medium_id'] != '0') ? $_POST['medium_id'] : null;
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $imageName = uniqid() . '_' . $filename;
            $uploadPath = 'uploads/' . $imageName;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $message = "Error uploading image.";
                $messageType = "error";
                $imageName = '';
            }
        } else {
            $message = "Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.";
            $messageType = "error";
        }
    }
    if (empty($message) || $messageType != 'error') {
        try {
            $stmt = $pdo->prepare("INSERT INTO product (title, price, description, image, artist_id, type_id, medium_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $price, $description, $imageName, $artist_id, $type_id, $medium_id]);
            
            $message = "Product added successfully!";
            $messageType = "success";
            header("refresh:2;url=manage_products.php");
        } catch(PDOException $e) {
            $message = "Error adding product: " . $e->getMessage();
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
    <title>Add Product - Art Gallery</title>
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
            display: inline-block;
            margin-bottom: 25px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
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
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .required {
            color: #dc3545;
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
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
        }
        
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        select {
            cursor: pointer;
            background: white;
        }
        
        .image-preview {
            margin-top: 15px;
            display: none;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
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
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .help-text {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .btn-container {
                flex-direction: column;
            }
            
            .content {
                padding: 25px;
            }
        }
    </style>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎨 Add New Product</h1>
            <p>Add a new artwork to your gallery</p>
        </div>
        
        <div class="content">
            <a href="manage_products.php" class="back-link">← Back to Products</a>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="name">Product Name <span class="required">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           placeholder="e.g., Starry Night Painting">
                    <div class="help-text">Enter the title or name of the artwork</div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="artist_id">Artist <span class="required">*</span></label>
                        <select id="artist_id" name="artist_id" required>
                            <option value="">-- Select Artist --</option>
                            <?php foreach ($artists as $artist): ?>
                                <option value="<?php echo $artist['id']; ?>">
                                    <?php echo htmlspecialchars($artist['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-text">
                            <?php if (count($artists) == 0): ?>
                                <span style="color: #dc3545;">No artists found. <a href="manage_artists.php">Add artists first</a></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="type_id">Art Type <span class="required">*</span></label>
                        <select id="type_id" name="type_id" required>
                            <option value="">-- Select Art Type --</option>
                            <?php foreach ($artTypes as $type): ?>
                                <option value="<?php echo $type['id']; ?>">
                                    <?php echo htmlspecialchars($type['type_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-text">
                            <?php if (count($artTypes) == 0): ?>
                                <span style="color: #dc3545;">No art types found. <a href="manage_art_types.php">Add art types first</a></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if (count($artMediums) > 0): ?>
                <div class="form-group">
                    <label for="medium_id">Art Medium (Optional)</label>
                    <select id="medium_id" name="medium_id">
                        <option value="">-- Select Medium (Optional) --</option>
                        <?php foreach ($artMediums as $medium): ?>
                            <option value="<?php echo $medium['id']; ?>">
                                <?php echo htmlspecialchars($medium['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="help-text">e.g., Oil on Canvas, Watercolor, Digital, etc.</div>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="price">Price (₹) <span class="required">*</span></label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           step="0.01" 
                           min="0" 
                           required 
                           placeholder="0.00">
                    <div class="help-text">Enter the price in Indian Rupees</div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" 
                              name="description" 
                              placeholder="Describe the artwork, its inspiration, techniques used, etc."></textarea>
                    <div class="help-text">Optional: Add details about the artwork</div>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image <span class="required">*</span></label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           accept="image/*" 
                           required
                           onchange="previewImage(this)">
                    <div class="help-text">Accepted formats: JPG, JPEG, PNG, GIF, WEBP</div>
                    
                    <div id="imagePreview" class="image-preview">
                        <img id="previewImg" src="" alt="Preview">
                    </div>
                </div>
                
                <div class="btn-container">
                    <button type="submit" name="add_product" class="btn btn-primary">
                        ✓ Add Product
                    </button>
                    <a href="manage_products.php" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>