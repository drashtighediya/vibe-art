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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_type'])) {
        $typeName = trim($_POST['type_name']);
        
        if (!empty($typeName)) {
            $stmt = $pdo->prepare("INSERT INTO arttype (type_name) VALUES (?)");
            if ($stmt->execute([$typeName])) {
                $message = "Art type added successfully!";
                $messageType = "success";
            } else {
                $message = "Error adding art type.";
                $messageType = "error";
            }
        } else {
            $message = "Type name is required.";
            $messageType = "error";
        }
    }
    if (isset($_POST['update_type'])) {
        $typeId = $_POST['type_id'];
        $typeName = trim($_POST['type_name']);
        
        if (!empty($typeName)) {
            $stmt = $pdo->prepare("UPDATE arttype SET type_name = ? WHERE id = ?");
            if ($stmt->execute([$typeName, $typeId])) {
                $message = "Art type updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating art type.";
                $messageType = "error";
            }
        }
    }
    if (isset($_POST['delete_type'])) {
        $typeId = $_POST['type_id'];
        
        $stmt = $pdo->prepare("DELETE FROM arttype WHERE id = ?");
        if ($stmt->execute([$typeId])) {
            $message = "Art type deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting art type. It may be in use.";
            $messageType = "error";
        }
    }
}
$stmt = $pdo->query("SELECT * FROM arttype ORDER BY type_name ASC");
$artTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$editType = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM arttype WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editType = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Art Types - Art Gallery</title>
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
            max-width: 1200px;
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
            padding: 30px;
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
        
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .form-section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
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
        
        .btn-edit {
            background: #ffc107;
            color: #333;
            padding: 8px 15px;
            font-size: 12px;
            margin-right: 5px;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            font-size: 12px;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .actions {
            white-space: nowrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎨 Manage Art Types</h1>
            <p>Add, edit, and organize art categories for your gallery</p>
        </div>
        
        <div class="content">
            <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <div class="form-section">
                <h2><?php echo $editType ? 'Edit Art Type' : 'Add New Art Type'; ?></h2>
                <form method="POST" action="">
                    <?php if ($editType): ?>
                        <input type="hidden" name="type_id" value="<?php echo $editType['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="type_name">Type Name *</label>
                        <input type="text" 
                               id="type_name" 
                               name="type_name" 
                               value="<?php echo $editType ? htmlspecialchars($editType['type_name']) : ''; ?>" 
                               required 
                               placeholder="e.g., Painting, Sculpture, Photography">
                    </div>
                    
                    <div>
                        <?php if ($editType): ?>
                            <button type="submit" name="update_type" class="btn btn-primary">Update Art Type</button>
                            <a href="manage_art_types.php" class="btn btn-cancel">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_type" class="btn btn-primary">Add Art Type</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div>
                <h2 style="color: #667eea; margin-bottom: 20px;">Existing Art Types</h2>
                
                <?php if (count($artTypes) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($artTypes as $type): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($type['id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($type['type_name']); ?></strong></td>
                                    <td class="actions">
                                        <a href="?edit=<?php echo $type['id']; ?>" class="btn btn-edit">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this art type?');">
                                            <input type="hidden" name="type_id" value="<?php echo $type['id']; ?>">
                                            <button type="submit" name="delete_type" class="btn btn-delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No art types found. Add your first art type above!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>