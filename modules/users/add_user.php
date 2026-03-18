<?php
include('../../includes/header.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $role = $_POST['role'];
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
     
    if (empty($name) || empty($user) || empty($pass) || empty($role)) {
        $error = "All fields are required.";
    } else { 
        $stmt_check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $user);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Username '$user' already exists. Please choose another.";
        } else { 
            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, username, password, role) VALUES (?, ?, SHA2(?, 256), ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $name, $user, $pass, $role);
            
            if (mysqli_stmt_execute($stmt)) {
                if(function_exists('logActivity')) { 
                    require_once('../../includes/logger.php'); 
                    logActivity($conn, $_SESSION['user_id'], "User Mgmt", "Created user: $name ($role)"); 
                }
                $_SESSION['success_msg'] = "<strong>Success!</strong> Account for $name has been created.";
                header("Location: manage_users.php");
                exit();
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="panel" style="max-width: 500px; margin: 0 auto;">
    <div class="panel-header"><h2>Create User Account</h2></div>
    
    <?php if($error): ?>
        <div style="background:#fee2e2; color:#991b1b; padding:10px; margin-bottom:15px; border-radius: 4px;">
            <?= $error ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" novalidate>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" placeholder="e.g. Juan Cruz">
        </div>
        
        <div class="form-group">
            <label>Role</label> 
            <select name="role" class="form-control">
                <option value="Front Desk Staff">Front Desk Staff</option>
                <option value="Inventory Clerk">Inventory Clerk</option>
                <option value="Fleet Coordinator">Fleet Coordinator</option>
                <option value="Administrator">Administrator</option>
                <option value="Driver">Driver (Can log in to see schedule)</option>
            </select>
        </div>
  
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">Create Account</button>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>