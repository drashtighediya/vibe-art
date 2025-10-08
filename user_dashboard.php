<?php
session_start();
$conn = new mysqli("localhost", "root", "", "art_gallery");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT full_name, email, created_at FROM users WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows == 0) {
    die("User not found or query failed!");
}
$userData = $result->fetch_assoc();
$stmt->close();

function getCount($conn, $table, $user_id, $status = null) {
    if ($status === "NULL") {
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE user_id = ? AND status IS NULL";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param("i", $user_id);
    } elseif ($status !== null) {
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE user_id = ? AND status = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param("is", $user_id, $status);
    } else {
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();
    return $count;
}

$totalOrders = 0;
$completedOrders = 0;
$pendingOrders = 0;
$totalEnquiry = 0;
$answeredEnquiry = 0;
$unansweredEnquiry = 0;

$tableCheck = $conn->query("SHOW TABLES LIKE 'orders'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $totalOrders = getCount($conn, "orders", $user_id);
    
    $columnCheck = $conn->query("SHOW COLUMNS FROM orders LIKE 'status'");
    if ($columnCheck && $columnCheck->num_rows > 0) {
        $completedOrders = getCount($conn, "orders", $user_id, "Completed");
        $pendingOrders = getCount($conn, "orders", $user_id, "Pending");
    }
}

$tableCheck = $conn->query("SHOW TABLES LIKE 'enquiry'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $totalEnquiry = getCount($conn, "enquiry", $user_id);
    
    $columnCheck = $conn->query("SHOW COLUMNS FROM enquiry LIKE 'status'");
    if ($columnCheck && $columnCheck->num_rows > 0) {
        $answeredEnquiry = getCount($conn, "enquiry", $user_id, "Answered");
        $unansweredEnquiry = getCount($conn, "enquiry", $user_id, "NULL");
    }
}

$wishlistCount = getCount($conn, "wishlist", $user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dash.css">
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
        <h2 class="title">MY DASHBOARD</h2>
        <div class="dashboard">
            <div class="card pink">Total Orders: <span><?php echo $totalOrders; ?></span></div>
            <div class="card blue">Completed Orders: <span><?php echo $completedOrders; ?></span></div>
            <div class="card dark">Pending Orders: <span><?php echo $pendingOrders; ?></span></div>
            <div class="card pink">Total Enquiries: <span><?php echo $totalEnquiry; ?></span></div>
            <div class="card blue">Answered Enquiries: <span><?php echo $answeredEnquiry; ?></span></div>
            <div class="card dark">Unanswered Enquiries: <span><?php echo $unansweredEnquiry; ?></span></div>
        </div>

        <h3 style="margin-top:20px;">My Info</h3>
        <p><b>Name:</b> <?php echo htmlspecialchars($userData['full_name']); ?></p>
        <p><b>Email:</b> <?php echo htmlspecialchars($userData['email']); ?></p>
        <p><b>Member Since:</b> <?php echo htmlspecialchars($userData['created_at']); ?></p>
    </div>
</body>
</html>