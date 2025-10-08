<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if medium ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_art_mediums.php");
    exit();
}

$medium_id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch existing medium data
$stmt = $conn->prepare("SELECT * FROM artmedium WHERE id = ?");

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error . "<br>Please check if the 'artmedium' table exists in your database.");
}

$stmt->bind_param("i", $medium_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_art_mediums.php");
    exit();
}

$medium = $result->fetch_assoc();
$stmt->close();

// Set default values if keys don't exist
if (!isset($medium['medium_name'])) $medium['medium_name'] = '';
if (!isset($medium['description'])) $medium['description'] = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medium_name = trim($_POST['medium_name']);
    $description = trim($_POST['description']);
    
    if (empty($medium_name)) {
        $error = "Medium name is required.";
    } else {
        // Check for duplicate
        $check_stmt = $conn->prepare("SELECT id FROM artmedium WHERE medium_name = ? AND id != ?");
        
        if ($check_stmt === false) {
            $error = "Database error: " . $conn->error;
        } else {
            $check_stmt->bind_param("si", $medium_name, $medium_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error = "An art medium with this name already exists.";
                $check_stmt->close();
            } else {
                $check_stmt->close();
                $update_stmt = $conn->prepare("UPDATE artmedium SET medium_name = ?, description = ? WHERE id = ?");
                
                if ($update_stmt === false) {
                    $error = "Database error: " . $conn->error;
                } else {
                    $update_stmt->bind_param("ssi", $medium_name, $description, $medium_id);
                    
                    if ($update_stmt->execute()) {
                        $success = "Art medium updated successfully!";
                        $medium['medium_name'] = $medium_name;
                        $medium['description'] = $description;
                    } else {
                        $error = "Error updating: " . $conn->error;
                    }
                    
                    $update_stmt->close();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Art Medium</title>
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
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .form-container {
            padding: 40px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-error {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background-color: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .required {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Art Medium</h1>
            <p>Update art medium information</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="medium_name">Medium Name <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="medium_name" 
                        name="medium_name" 
                        value="<?php echo htmlspecialchars($medium['medium_name']); ?>" 
                        required
                        placeholder="e.g., Oil Painting, Watercolor, Digital Art"
                    >
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        placeholder="Optional description of the art medium"
                    ><?php echo htmlspecialchars($medium['description']); ?></textarea>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Update Medium</button>
                    <a href="manage_art_mediums.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>