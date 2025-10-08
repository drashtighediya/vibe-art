<?php
session_start();
include 'config.php';
if (isset($_SESSION["user"])) { 
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: home.php");
    }
    exit();
}

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user["password"])) {    
            
            $_SESSION["user"] = $user;
            $_SESSION["user_id"] = $user["id"]; 
            if (isset($user['role']) && $user['role'] === 'admin') {
                $_SESSION['is_admin'] = true;
            } else {
                $_SESSION['is_admin'] = false;
            }
            if ($_SESSION['is_admin'] === true) {
                header("Location: admin_dashboard.php");
            } else if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
                header("Location: " . $_GET['redirect']);
            } else {
                header("Location: home.php");
            }

        } else {
            $error = "Password does not match";
        }  
    } else {
        $error = "Email does not match";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="logo">
            <img src="image/logo.jpg" alt="Logo">
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="login.php<?php if(isset($_GET['redirect'])) echo '?redirect='.urlencode($_GET['redirect']); ?>" method="post">
            <h1 class="text-center mb-3">Login</h1>

            <div class="form-group mb-3">
                <input type="email" placeholder="Enter Email:" name="email" class="form-control" required>
            </div>

            <div class="form-group mb-2">
                <input type="password" placeholder="Enter Password:" name="password" id="password" class="form-control" required>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" onclick="togglePassword()" id="showPassword">
                    <label class="form-check-label underline-label" for="showPassword" style="text-decoration: underline;">Show Password</label>
                </div>
            </div>

            <div class="form btn mt-3">
                <button type="submit" name="login" class="login-button">Login</button>
            </div>
        </form>

        <div class="text-center mt-3" style="font-size:1rem;">
            <span>Don't have an account? </span>
            <a href="registration.php" style="color:#4a00e0; text-decoration:underline; font-weight:500;">Register Here</a>
        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }
    </script>
</body>
</html>