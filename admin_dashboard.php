<?php
$conn = new mysqli("localhost", "root", "", "art_gallery");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$artists    = $conn->query("SELECT COUNT(*) AS total FROM artists")->fetch_assoc()['total'] ?? 0;
$artType    = $conn->query("SELECT COUNT(*) AS total FROM arttype")->fetch_assoc()['total'] ?? 0;
$artMedium  = $conn->query("SELECT COUNT(*) AS total FROM artmedium")->fetch_assoc()['total'] ?? 0;
$artProduct = $conn->query("SELECT COUNT(*) AS total FROM product")->fetch_assoc()['total'] ?? 0;
$result     = $conn->query("SELECT COUNT(*) AS total FROM enquiry WHERE status IS NULL");
$unanswered = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
$result     = $conn->query("SELECT COUNT(*) AS total FROM enquiry WHERE status='Answered'");
$answered   = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dash.css">
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
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_artists.php">Artists</a></li>
            <li><a href="manage_art_types.php">Art Types</a></li>
            <li><a href="manage_art_mediums.php">Art Mediums</a></li>
            <li><a href="manage_products.php">Products</a></li>
            <li><a href="home.php">Logout</a></li>
        </ul>
    </div>
    <div class="center-content">
    <h2 class="title">DASHBOARD</h2>
    <div class="dashboard">
        <div class="card pink">Total Artists: <span><?php echo $artists; ?></span></div>
        <div class="card blue">Unanswered Enquiries: <span><?php echo $unanswered; ?></span></div>
        <div class="card pink">Answered Enquiries: <span><?php echo $answered; ?></span></div>
        <div class="card dark">Art Types: <span><?php echo $artType; ?></span></div>
        <div class="card blue">Art Mediums: <span><?php echo $artMedium; ?></span></div>
        <div class="card dark">Art Products: <span><?php echo $artProduct; ?></span></div>
    </div>
</div>
</body>
</html>