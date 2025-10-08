<?php
session_start();
$conn = new mysqli("localhost", "root", "", "art_gallery");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM artmedium WHERE id = $id");
    header("Location: manage_art_mediums.php");
    exit;
}

$result = $conn->query("SELECT * FROM artmedium ORDER BY id DESC");
$totalMediums = $result ? $result->num_rows : 0;
$columnsResult = $conn->query("SHOW COLUMNS FROM artmedium LIKE 'description'");
$hasDescription = ($columnsResult && $columnsResult->num_rows > 0);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Art Mediums - VibeArt Gallery</title>
    <link rel="stylesheet" href="dash.css">
    <style>
        .container {
            margin-left: 270px;
            padding: 30px;
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
        }
        .btn-success {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
            font-size: 16px;
            padding: 14px 28px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        .btn-success::before {
            content: "➕";
            font-size: 20px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        .btn-success::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #45a049 0%, #388e3c 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.5);
        }
        .btn-success:hover::after {
            width: 300px;
            height: 300px;
        }
        .btn-success:active {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.4);
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid #2196f3;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 36px;
            color: #2196f3;
        }
        .stat-box p {
            margin: 10px 0 0 0;
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }
        .stat-box.purple {
            border-left-color: #9c27b0;
        }
        .stat-box.purple h3 {
            color: #9c27b0;
        }
        .stat-box.teal {
            border-left-color: #009688;
        }
        .stat-box.teal h3 {
            color: #009688;
        }
        
        .medium-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .medium-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border-top: 4px solid #2196f3;
        }
        .medium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .medium-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .medium-name {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        .medium-id {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .medium-description {
            color: #666;
            line-height: 1.6;
            margin: 15px 0;
            font-size: 14px;
        }
        .medium-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .medium-actions a {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            transition: all 0.2s;
        }
        .btn-edit {
            background: #2196f3;
            color: white;
        }
        .btn-edit:hover {
            background: #0b7dda;
        }
        .btn-delete {
            background: #f44336;
            color: white;
        }
        .btn-delete:hover {
            background: #da190b;
        }
        .no-data {
            text-align: center;
            padding: 60px;
            color: #999;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .no-data h3 {
            color: #666;
            margin-bottom: 10px;
        }
        .navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 999;
}

.sidebar {
    position: fixed;
    z-index: 998;
}

.container {
    margin-top: 60px; 
    position: relative;
    z-index: 1;
}
        .view-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .view-btn {
            padding: 10px 20px;
            border: 2px solid #2196f3;
            background: white;
            color: #2196f3;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .view-btn.active {
            background: #2196f3;
            color: white;
        }
        table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table th {
            background: #2196f3;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        table tr:hover {
            background: #f9f9f9;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        .action-links a {
            margin-right: 10px;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
        }
        .table-view {
            display: none;
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
            <h2>🎨 Manage Art Mediums</h2>
            <a href="add_art_medium.php" class="btn btn-success">Add New Art Medium</a>
        </div>

        <?php
        if($result) $result->data_seek(0);
        $productCounts = [];
        $mediumProductCounts = $conn->query("
            SELECT artmedium_id, COUNT(*) as count 
            FROM product 
            GROUP BY artmedium_id
        ");
        
        if($mediumProductCounts) {
            while($row = $mediumProductCounts->fetch_assoc()) {
                $productCounts[$row['artmedium_id']] = $row['count'];
            }
        }
        
        $totalProducts = array_sum($productCounts);
        $mostUsed = $productCounts ? max($productCounts) : 0;
        ?>

        <div class="stats-summary">
            <div class="stat-box">
                <h3><?= $totalMediums ?></h3>
                <p>Total Art Mediums</p>
            </div>
            <div class="stat-box purple">
                <h3><?= $totalProducts ?></h3>
                <p>Products Using Mediums</p>
            </div>
            <div class="stat-box teal">
                <h3><?= $mostUsed ?></h3>
                <p>Most Used Medium</p>
            </div>
        </div>

        <?php if($result && $result->num_rows > 0): ?>
        <div class="medium-grid">
            <?php 
            $result->data_seek(0);
            while($row = $result->fetch_assoc()): 
                $productCount = $productCounts[$row['id']] ?? 0;
                $mediumName = isset($row['medium_name']) ? $row['medium_name'] : (isset($row['name']) ? $row['name'] : 'Unknown');
                $mediumDesc = $hasDescription && isset($row['description']) ? $row['description'] : 'No description available';
            ?>
            <div class="medium-card">
                <div class="medium-header">
                    <h3 class="medium-name"><?= htmlspecialchars($mediumName) ?></h3>
                    <span class="medium-id">#<?= $row['id'] ?></span>
                </div>
                <?php if($hasDescription): ?>
                <p class="medium-description">
                    <?= htmlspecialchars($mediumDesc) ?>
                </p>
                <?php endif; ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                    <small style="color: #2196f3; font-weight: 600;">
                        📦 <?= $productCount ?> Product<?= $productCount != 1 ? 's' : '' ?> using this medium
                    </small>
                </div>
                <div class="medium-actions">
                    <a href="edit_art_medium.php?id=<?= $row['id'] ?>" class="btn-edit">✏️ Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn-delete" 
                       onclick="return confirm('Are you sure you want to delete this art medium?\n\nNote: This has <?= $productCount ?> product(s) associated with it.')">
                       🗑️ Delete
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="table-view">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Medium Name</th>
                        <?php if($hasDescription): ?>
                        <th>Description</th>
                        <?php endif; ?>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $result->data_seek(0);
                    while($row = $result->fetch_assoc()): 
                        $productCount = $productCounts[$row['id']] ?? 0;
                        $mediumName = isset($row['medium_name']) ? $row['medium_name'] : (isset($row['name']) ? $row['name'] : 'Unknown');
                        $mediumDesc = $hasDescription && isset($row['description']) ? $row['description'] : 'No description';
                    ?>
                    <tr>
                        <td><strong>#<?= $row['id'] ?></strong></td>
                        <td><strong><?= htmlspecialchars($mediumName) ?></strong></td>
                        <?php if($hasDescription): ?>
                        <td><?= htmlspecialchars(substr($mediumDesc, 0, 100)) ?><?= strlen($mediumDesc) > 100 ? '...' : '' ?></td>
                        <?php endif; ?>
                        <td><?= $productCount ?> product(s)</td>
                        <td class="action-links">
                            <a href="edit_art_medium.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                            <a href="?delete=<?= $row['id'] ?>" class="btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this art medium?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php else: ?>
        <div class="no-data">
            <h3>🎨 No Art Mediums Found</h3>
            <p>Add art mediums like Oil Paint, Watercolor, Digital Art, etc.</p>
            <br>
            <a href="add_art_medium.php" class="btn btn-success">Add Your First Art Medium</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>