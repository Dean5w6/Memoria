<?php
include('../../includes/header.php');

if ($_SESSION['role'] !== 'Admin') {
    die("<div class='panel'><h2>Access Denied</h2><p>Only Administrators can manage accounts.</p></div>");
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('You cannot delete your own account.');</script>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        $_SESSION['success_msg'] = "User account deleted.";
        header("Location: manage_users.php");
        exit();
    }
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
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
    </div>
<?php endif; ?>

<div class="panel">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left;">
                <th style="padding: 15px;">Full Name</th>
                <th style="padding: 15px;">Username</th>
                <th style="padding: 15px;">Role</th>
                <th style="padding: 15px;">Created At</th>
                <th style="padding: 15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
            while ($row = mysqli_fetch_assoc($q)) {
                echo "<tr>
                    <td style='padding: 15px; border-bottom: 1px solid #eee; font-weight: 500;'>{$row['full_name']}</td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee;'>{$row['username']}</td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee;'><span style='background:#e3f2fd; color:#1565c0; padding:2px 8px; border-radius:4px; font-size:0.8rem;'>{$row['role']}</span></td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee; color:#777;'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                    <td style='padding: 15px; border-bottom: 1px solid #eee;'>
                        <a href='edit_user.php?id={$row['id']}' class='btn' style='background:#eee; padding:5px 10px; font-size:0.8rem;'><i class='fas fa-edit'></i></a>
                        <a href='manage_users.php?delete={$row['id']}' onclick='return confirm(\"Delete this user?\")' class='btn' style='background:#fee2e2; color:#991b1b; padding:5px 10px; font-size:0.8rem;'><i class='fas fa-trash'></i></a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<?php include('../../includes/footer.php'); ?>