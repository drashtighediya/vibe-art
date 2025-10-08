<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="logo">
            <img src="image/logo.jpg" alt="Logo">
        </div>
        <?php 
       if(isset($_POST["register"])) {
        $fullName = trim($_POST["fullname"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $passwordConfirm = $_POST["confirm_password"];
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $errors = array();

        if (empty($fullName) OR empty($email) OR empty($password) OR empty($passwordConfirm)) {
            array_push($errors,"All fields are required");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
        }
        if (strlen($password)<8) {
            array_push($errors,"Password must be at least 8 character long");
        }
        if($password!==$passwordConfirm){
            array_push($errors,"Password does not match");
        }
        require_once "database.php";
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);
        if($rowCount>0) {
            array_push($errors,"Email already exists");
        }
        if(count($errors)>0) {
            foreach ($errors as $error) {
                echo "<div class= 'alert alert-danger'>$error</div>";
        } 
        }else{
        $sql = "INSERT INTO users(full_name, email, password) VALUES ( ?, ?, ? )";
        $stmt =  mysqli_stmt_init($conn);
        $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
        if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash );
            mysqli_stmt_execute($stmt);
        
            
            header("Location:login.php");
            exit();
        }else{
            die("something went wrong");
        }
    }


    }
        ?>
        <form action="registration.php" method="post"> 
        <h2 class="text-center mb-3">Registration</h2>
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="confirm_password"  id ="confirm_password" placeholder="Confirm Password:">
                <div class="form-check mt-2" >
                    <input class="form-check-input" type="checkbox" onclick="togglePassword()"  id="showPassword">
                    <label class="form-check-label underline-label" for="showPassword" style="text-decoration: underline;">Show Password</label>
                </div>
            </div>
            <div class="form-btn mt-3">
            <button type="submit" name="register" class="login-button">Register</button>
            </div>
        </form>
                <div class="text-center mt-3" style="font-size:1rem;">
                    <span>Already have an account? </span>
                    <a href="login.php" style="color:#4a00e0; text-decoration:underline; font-weight:500;">Login Here</a>
                </div>
    </div>
</body>
<script>
function togglePassword() {
    const password = document.querySelector('input[name="password"]');
    const confirmPassword = document.querySelector('input[name="confirm_password"]');
    if (password.type === "password") {
        password.type = "text";
        confirmPassword.type = "text";
    } else {
        password.type = "password";
        confirmPassword.type = "password";
    }
}
</script>
</html>