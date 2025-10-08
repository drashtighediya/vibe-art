<?php
session_start();
$conn = new mysqli("localhost", "root", "", "art_gallery");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$isLoggedIn = (
    isset($_SESSION['user_id']) || 
    isset($_SESSION['username']) || 
    isset($_SESSION['user_email']) ||
    isset($_SESSION['email']) ||
    isset($_SESSION['loggedin']) ||
    isset($_SESSION['user'])
);

if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
}
$user_id = null;
if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
} elseif (isset($_SESSION['user']['id'])) {
    $user_id = (int) $_SESSION['user']['id'];
} elseif (isset($_SESSION['id'])) {
    $user_id = (int) $_SESSION['id'];
}
if (!$user_id && isset($_SESSION['user_email'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['user_email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_id = $result->fetch_assoc()['id'];
    }
    $stmt->close();
} elseif (!$user_id && isset($_SESSION['email'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_id = $result->fetch_assoc()['id'];
    }
    $stmt->close();
}
if (!$user_id) {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    if (empty($full_name) || empty($email)) {
        $error = "Name and Email are required!";
    } else {
        $columns_to_update = [];
        $values = [];
        $types = "";
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'full_name'");
        if ($result->num_rows > 0) {
            $columns_to_update[] = "full_name = ?";
            $values[] = $full_name;
            $types .= "s";
        }
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'username'");
        if ($result->num_rows > 0 && !in_array("full_name = ?", $columns_to_update)) {
            $columns_to_update[] = "username = ?";
            $values[] = $full_name;
            $types .= "s";
        }
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
        if ($result->num_rows > 0) {
            $columns_to_update[] = "email = ?";
            $values[] = $email;
            $types .= "s";
        }
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
        if ($result->num_rows > 0) {
            $columns_to_update[] = "phone = ?";
            $values[] = $phone;
            $types .= "s";
        }
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'address'");
        if ($result->num_rows > 0) {
            $columns_to_update[] = "address = ?";
            $values[] = $address;
            $types .= "s";
        }
        
        if (count($columns_to_update) > 0) {
            $values[] = $user_id;
            $types .= "i";
            
            $sql = "UPDATE users SET " . implode(", ", $columns_to_update) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param($types, ...$values);
                
                if ($stmt->execute()) {
                    $message = "Profile updated successfully!";
                    if (isset($_SESSION['user'])) {
                        $_SESSION['user']['full_name'] = $full_name;
                        $_SESSION['user']['email'] = $email;
                    }
                    $_SESSION['user_email'] = $email;
                    $_SESSION['email'] = $email;
                    $_SESSION['username'] = $full_name;
                } else {
                    $error = "Error updating profile: " . $conn->error;
                }
                $stmt->close();
            } else {
                $error = "Database error: " . $conn->error;
            }
        } else {
            $error = "No valid columns found to update!";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All password fields are required!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($stmt->execute()) {
                $message = "Password changed successfully!";
            } else {
                $error = "Error changing password!";
            }
            $stmt->close();
        } else {
            $error = "Current password is incorrect!";
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlistCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - User Dashboard</title>
    <link rel="stylesheet" href="dash.css">
    <style>
        .profile-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #764ba2;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 15px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-textarea {
            min-height: 100px;
            resize: vertical;
            font-family: Arial, sans-serif;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
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
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .info-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
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
        <h2 class="title">MY PROFILE</h2>
        
        <div class="profile-container">
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="profile-card">
                <h3 class="section-title">Account Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">User ID</div>
                        <div class="info-value">#<?php echo $userData['id']; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Member Since</div>
                        <div class="info-value"><?php echo date('F j, Y', strtotime($userData['created_at'])); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="profile-card">
                <h3 class="section-title">Edit Profile</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-input" 
                               value="<?php echo htmlspecialchars($userData['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-input" 
                               value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-input" 
                               value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-input form-textarea"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
            
            <div class="profile-card">
                <h3 class="section-title">Change Password</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Current Password *</label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">New Password *</label>
                        <input type="password" name="new_password" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm New Password *</label>
                        <input type="password" name="confirm_password" class="form-input" required>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>