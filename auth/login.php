<?php
session_start();
require_once('../config/db.php');


if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        
        $sql = "SELECT id, full_name, role FROM users WHERE username = ? AND password = SHA2(?, 256)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $username;
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['LAST_ACTIVITY'] = time(); 
                
                
                
                if(file_exists('../includes/logger.php')) {
                    require_once('../includes/logger.php');
                    logActivity($conn, $row['id'], "Login", "User logged into the system.");
                }
                

                header("Location: " . BASE_URL . "dashboard.php");
                exit();
            } else {
                $error = "Invalid credentials. Please try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Database connection error.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | Memoria</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card"> 
            <img src="<?= BASE_URL ?>assets/img/logo.png" alt="Memoria Logo" class="login-logo">
            
            <h2>Welcome Back</h2>
            <p>Enter your credentials to access the Memoria System</p>
            
            <?php if($error): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; text-align: left;">
                    <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
                </div>
            <?php endif; ?>
 
            <form method="POST" novalidate>
                <div style="text-align: left; margin-bottom: 5px; font-weight: 500; font-size: 0.9rem;">Username</div>
                <input type="text" name="username" class="form-control" placeholder="e.g. admin">
                
                <div style="text-align: left; margin-bottom: 5px; font-weight: 500; font-size: 0.9rem;">Password</div>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
                
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                    Sign In <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>