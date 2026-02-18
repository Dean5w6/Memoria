<?php
include('../../includes/header.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $role = $_POST['role'];

    if (empty($name) || empty($user) || empty($pass)) {
        $error = "All fields are required.";
    } else {
        
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$user'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username already exists.";
        } else {
            
            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, username, password, role) VALUES (?, ?, SHA2(?, 256), ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $name, $user, $pass, $role);
            
            if (mysqli_stmt_execute($stmt)) {
                if(function_exists('logActivity')) { require_once('../../includes/logger.php'); logActivity($conn, $_SESSION['user_id'], "User Mgmt", "Created user: $user ($role)"); }
                $_SESSION['success_msg'] = "User account created successfully.";
                header("Location: manage_users.php");
                exit();
            } else {
                $error = "Database Error.";
            }
        }
    }
}
?>

<div class="panel" style="max-width: 500px; margin: 0 auto;">
    <div class="panel-header"><h2>Create User Account</h2></div>
    <?php if($error): ?><div style="background:#fee2e2; color:#991b1b; padding:10px; margin-bottom:15px;"><?= $error ?></div><?php endif; ?>
    
    <form method="POST" novalidate>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" placeholder="e.g. Juan Cruz" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="Staff">Staff (General)</option>
                <option value="Manager">Manager</option>
                <option value="Driver">Driver (Fleet)</option>
                <option value="Admin">Administrator</option>
            </select>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Create Account</button>
    </form>
</div>
<?php include('../../includes/footer.php'); ?>