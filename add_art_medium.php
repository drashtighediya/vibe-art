<?php
session_start();
$conn = new mysqli("localhost", "root", "", "art_gallery");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    if (empty($name)) {
        $error = "Art medium name is required!";
    } elseif (strlen($name) < 2) {
        $error = "Art medium name must be at least 2 characters long!";
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM artmedium WHERE medium_name = ?");
        
        if ($checkStmt === false) {
            $error = "Database error: " . $conn->error;
        } else {
            $checkStmt->bind_param("s", $name);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "This art medium already exists!";
            } else {
                $columnsResult = $conn->query("SHOW COLUMNS FROM artmedium LIKE 'description'");
                
                if ($columnsResult && $columnsResult->num_rows > 0) {
                    $stmt = $conn->prepare("INSERT INTO artmedium (medium_name, description) VALUES (?, ?)");
                    
                    if ($stmt === false) {
                        $error = "Database error: " . $conn->error;
                    } else {
                        $stmt->bind_param("ss", $name, $description);
                        
                        if ($stmt->execute()) {
                            $success = "Art medium added successfully!";
                            $name = "";
                            $description = "";
                        } else {
                            $error = "Error adding art medium: " . $stmt->error;
                        }
                        $stmt->close();
                    }
                } else {
                    $stmt = $conn->prepare("INSERT INTO artmedium (medium_name) VALUES (?)");
                    
                    if ($stmt === false) {
                        $error = "Database error: " . $conn->error;
                    } else {
                        $stmt->bind_param("s", $name);
                        
                        if ($stmt->execute()) {
                            $success = "Art medium added successfully!";
                            $name = "";
                            $description = "";
                        } else {
                            $error = "Error adding art medium: " . $stmt->error;
                        }
                        $stmt->close();
                    }
                }
            }
            $checkStmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Art Medium - VibeArt Gallery</title>
    <link rel="stylesheet" href="dash.css">
    <style>
        .container {
            margin-left: 270px;
            padding: 30px;
            max-width: 900px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            color: #333;
            font-size: 28px;
            margin: 0;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-secondary {
            background: #757575;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #616161;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .required {
            color: #f44336;
            margin-left: 3px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2196f3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-help {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .btn-primary {
            background: #2196f3;
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            background: #0b7dda;
        }
        
        .btn-reset {
            background: #ff9800;
            color: white;
        }
        
        .btn-reset:hover {
            background: #f57c00;
        }
        
        .examples-box {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
            border-left: 4px solid #2196f3;
        }
        
        .examples-box h4 {
            margin-top: 0;
            color: #333;
            font-size: 16px;
        }
        
        .examples-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .examples-box li {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }
        
        .char-counter {
            text-align: right;
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="image/logo.jpg" alt="Art Gallery Logo" style="height: 40px;">
        </div>
        <div class="admin-info">
            <span>Admin</span>
        </div>
    </div>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage_artists.php">Artists</a></li>
            <li><a href="manage_art_types.php">Art Types</a></li>
            <li><a href="manage_art_mediums.php" class="active">Art Mediums</a></li>
            <li><a href="manage_products.php">Products</a></li>
            <li><a href="home.php">Logout</a></li>
        </ul>
    </div>

    <div class="container">
        <div class="page-header">
            <h2>🎨 Add New Art Medium</h2>
            <a href="manage_art_mediums.php" class="btn btn-secondary">← Back to List</a>
        </div>

        <div class="form-container">
            <?php if ($success): ?>
            <div class="alert alert-success">
                ✓ <?= $success ?>
                <a href="manage_art_mediums.php" style="margin-left: auto; color: #155724; text-decoration: underline;">View all mediums</a>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-error">
                ✕ <?= $error ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" id="artMediumForm">
                <div class="form-group">
                    <label for="name">
                        Art Medium Name <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        placeholder="e.g., Oil Paint, Watercolor, Digital Art" 
                        value="<?= isset($name) ? htmlspecialchars($name) : '' ?>"
                        maxlength="100"
                        required
                    >
                    <div class="form-help">Enter a clear, concise name for the art medium</div>
                </div>

                <div class="form-group">
                    <label for="description">
                        Description <span class="required">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        placeholder="Describe the art medium, its characteristics, and typical uses..."
                        maxlength="1000"
                        required
                        oninput="updateCharCounter()"
                    ><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                    <div class="char-counter">
                        <span id="charCount">0</span> / 1000 characters
                    </div>
                    <div class="form-help">Provide details about the medium's properties, techniques, and common applications</div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        ✓ Add Art Medium
                    </button>
                    <button type="reset" class="btn btn-reset" onclick="resetCharCounter()">
                        ↺ Reset Form
                    </button>
                </div>
            </form>

            <div class="examples-box">
                <h4>💡 Example Art Mediums:</h4>
                <ul>
                    <li><strong>Oil Paint:</strong> Traditional painting medium known for rich colors and slow drying time, allowing for blending and layering techniques.</li>
                    <li><strong>Watercolor:</strong> Transparent water-based paint that creates luminous, flowing effects on paper.</li>
                    <li><strong>Acrylic:</strong> Fast-drying, versatile paint that can be used on various surfaces with water-soluble properties.</li>
                    <li><strong>Digital Art:</strong> Artwork created using digital tools, software, and tablets for modern creative expression.</li>
                    <li><strong>Charcoal:</strong> Drawing medium that produces bold, dramatic lines and rich tonal values.</li>
                    <li><strong>Mixed Media:</strong> Combination of multiple artistic mediums in a single artwork.</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function updateCharCounter() {
            const textarea = document.getElementById('description');
            const counter = document.getElementById('charCount');
            counter.textContent = textarea.value.length;
        }

        function resetCharCounter() {
            setTimeout(() => {
                document.getElementById('charCount').textContent = '0';
            }, 0);
        }
        window.addEventListener('DOMContentLoaded', updateCharCounter);
        document.getElementById('artMediumForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const description = document.getElementById('description').value.trim();

            if (name.length < 2) {
                e.preventDefault();
                alert('Art medium name must be at least 2 characters long!');
                return false;
            }

            if (description.length < 10) {
                e.preventDefault();
                alert('Please provide a more detailed description (at least 10 characters)!');
                return false;
            }
        });
    </script>
</body>
</html>