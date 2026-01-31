<?php
include('../../includes/header.php');


if($_SESSION['role'] != 'Admin') {
    echo "<div class='panel'><h3>Access Denied</h3></div>";
    include('../../includes/footer.php');
    exit();
}
?>

<div class="panel-header">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">System Audit Trail</h1>
        <p style="color: var(--text-light);">Log of all system activities for accountability.</p>
    </div>
</div>

<div class="panel">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left; color: var(--deep-navy);">
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Date/Time</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">User</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Action</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Details</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">IP Address</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT a.*, u.full_name, u.role 
                      FROM audit_logs a 
                      LEFT JOIN users u ON a.user_id = u.id 
                      ORDER BY a.created_at DESC LIMIT 50";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td style='padding: 12px; border-bottom: 1px solid #eee; color: #777; font-size: 0.85rem;'>" . date('M d, Y H:i:s', strtotime($row['created_at'])) . "</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>
                            <strong>{$row['full_name']}</strong><br>
                            <span style='font-size: 0.8rem; color: var(--slate-blue);'>{$row['role']}</span>
                        </td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee; font-weight: 600;'>{$row['action']}</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>{$row['details']}</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee; font-family: monospace;'>{$row['ip_address']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align: center; padding: 20px;'>No logs recorded yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('../../includes/footer.php'); ?>