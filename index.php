<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(to right, #667eea, #764ba2);
            min-height: 100vh;
        }
        .dashboard-container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(118,75,162,0.12);
            padding: 40px 32px;
            text-align: center;
        }
        .dashboard-heading {
            color: #764ba2;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            letter-spacing: 2px;
        }
        .user-info p {
            margin: 8px 0;
            color: #4a00e0;
            font-weight: 500;
        }
        .btn-primary {
            background: linear-gradient(to right, #8e2de2, #4a00e0);
            border: none;
            font-weight: 600;
            margin-right: 12px;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #764ba2, #667eea);
        }
        .btn-danger {
            background: linear-gradient(to right, #e04a4a, #764ba2);
            border: none;
            font-weight: 600;
        }
        .btn-danger:hover {
            background: linear-gradient(to right, #764ba2, #e04a4a);
        }
        </style>
</head>
<body>
    <div class="container mt-5 dashboarad-container">
        <h1 class="dashboard-heading">Welcome to Dashboard</h1>

        <p>Email: <?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
        <p>Member Since: <?php echo htmlspecialchars($_SESSION['user']['created_at']); ?></p>
        
        <a href="user_dashboard.php" style="margin-top:20px; display:inline-block;" class="btn btn-primary">Go To Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
