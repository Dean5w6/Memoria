<?php
include('../../includes/header.php');

if ($_SESSION['role'] !== 'Administrator') {
    die("Access Denied");
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$id = intval($_GET['id']);
$error = "";

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (empty($name)) {
        $error = "Full Name is required.";
    } else {
        if (!empty($password)) {
            
            $sql = "UPDATE users SET full_name = ?, role = ?, password = SHA2(?, 256) WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $name, $role, $password, $id);
        } else {
            
            $sql = "UPDATE users SET full_name = ?, role = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $name, $role, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            
            if(function_exists('logActivity')) {
                require_once('../../includes/logger.php');
                logActivity($conn, $_SESSION['user_id'], "User Update", "Updated account for: " . $user['username']);
            }

            $_SESSION['success_msg'] = "User details updated successfully.";
            header("Location: manage_users.php");
            exit();
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <div class="panel-header">
        <h2>Edit User: <?= htmlspecialchars($user['username']); ?></h2>
        <a href="manage_users.php" class="btn" style="background: var(--cloud-gray); color: var(--text-dark);">Cancel</a>
    </div>

    <?php if($error): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
            <?= $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" novalidate>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="Front Desk Staff" <?= $user['role'] == 'Front Desk Staff' ? 'selected' : '' ?>>Front Desk Staff</option>
                <option value="Inventory Clerk" <?= $user['role'] == 'Inventory Clerk' ? 'selected' : '' ?>>Inventory Clerk</option>
                <option value="Fleet Coordinator" <?= $user['role'] == 'Fleet Coordinator' ? 'selected' : '' ?>>Fleet Coordinator</option>
                <option value="Driver" <?= $user['role'] == 'Driver' ? 'selected' : '' ?>>Driver</option>
                <option value="Administrator" <?= $user['role'] == 'Administrator' ? 'selected' : '' ?>>Administrator</option>
            </select>
        </div>

        <div class="form-group">
            <label>New Password <small style="color: #777; font-weight: normal;">(Leave blank to keep current password)</small></label>
            <input type="password" name="password" class="form-control" placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">Update Account</button>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>