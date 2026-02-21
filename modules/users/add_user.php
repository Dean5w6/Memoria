<?php
include('../../includes/header.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $role = $_POST['role'];
    
    
    if ($role == 'Driver') {
        
        $user = 'driver_' . uniqid(); 
        $pass = 'no_login_required'; 
    } else {
        $user = trim($_POST['username']);
        $pass = $_POST['password'];
    }
    
    if (empty($name)) {
        $error = "Full Name is required.";
    } elseif ($role != 'Driver' && (empty($user) || empty($pass))) {
        $error = "Username and Password are required for users who log in.";
    } else {
        
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$user'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username already exists.";
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
            <input type="text" name="full_name" class="form-control" placeholder="e.g. Juan Cruz" required>
        </div>
        
        <div class="form-group">
            <label>Role</label> 
            <select name="role" id="roleSelect" class="form-control" onchange="toggleCredentials()" required>
                <option value="Front Desk Staff">Front Desk Staff</option>
                <option value="Inventory Clerk">Inventory Clerk</option>
                <option value="Fleet Coordinator">Fleet Coordinator</option>
                <option value="Administrator">Administrator</option>
                <option value="Driver">Driver (No Login Required)</option>
            </select>
        </div>
 
        <div id="credentialsSection">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">Create Account</button>
    </form>
</div>

<script>
    function toggleCredentials() {
        const role = document.getElementById('roleSelect').value;
        const section = document.getElementById('credentialsSection');
        
        if (role === 'Driver') {
            section.style.display = 'none';
        } else {
            section.style.display = 'block';
        }
    }
     
    document.addEventListener('DOMContentLoaded', toggleCredentials);
</script>

<?php include('../../includes/footer.php'); ?>