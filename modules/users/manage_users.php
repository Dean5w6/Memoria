<?php
include('../../includes/header.php');

if ($_SESSION['role'] !== 'Administrator') {
    echo "<div class='panel' style='text-align: center; padding: 50px;'>
            <h2 style='color: var(--danger);'><i class='fas fa-shield-alt'></i> Access Denied</h2>
            <p>Only Administrators can manage accounts.</p>
          </div>";
    include('../../includes/footer.php');
    exit();
}


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error_msg'] = "You cannot delete your own active session account.";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        
        
        if(function_exists('logActivity')) {
            require_once('../../includes/logger.php');
            logActivity($conn, $_SESSION['user_id'], "User Deleted", "Deleted User ID: $id");
        }

        $_SESSION['success_msg'] = "User account deleted successfully.";
    }
    header("Location: manage_users.php");
    exit();
}
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">User Management</h1>
        <p style="color: var(--text-light);">Manage system access for staff and drivers.</p>
    </div>
    <a href="add_user.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add New User</a>
</div>

<?php if (isset($_SESSION['success_msg'])): ?>
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid #166534;">
        <i class="fas fa-check-circle"></i> <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid #991b1b;">
        <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
    </div>
<?php endif; ?>

<div class="panel">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left; color: var(--deep-navy);">
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Full Name</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Username</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Role</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Created At</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
            while ($row = mysqli_fetch_assoc($q)) {
                
                $roleColor = '#e3f2fd'; 
                $roleText = '#1565c0';
                
                if($row['role'] == 'Administrator') { $roleColor = '#333'; $roleText = '#fff'; }
                if($row['role'] == 'Driver') { $roleColor = '#fff3cd'; $roleText = '#856404'; }

                echo "<tr>
                    <td style='padding: 15px; border-bottom: 1px solid #eee; font-weight: 500;'>" . htmlspecialchars($row['full_name']) . "</td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($row['username']) . "</td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee;'>
                        <span style='background: $roleColor; color: $roleText; padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight: 600;'>" . htmlspecialchars($row['role']) . "</span>
                    </td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee; color:#777;'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee;'>
                        <a href='edit_user.php?id={$row['id']}' class='btn' style='background:#eee; color: var(--deep-navy); padding:6px 10px; font-size:0.8rem; margin-right: 5px;' title='Edit'><i class='fas fa-edit'></i></a>
                        <a href='manage_users.php?delete={$row['id']}' onclick='return confirm(\"Permanently delete this user account?\")' class='btn' style='background:#fee2e2; color:#991b1b; padding:6px 10px; font-size:0.8rem;' title='Delete'><i class='fas fa-trash'></i></a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('../../includes/footer.php'); ?>