<?php 
include('includes/header.php'); 
$role = $_SESSION['role'];
 
$inv_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM items WHERE stock_quantity <= min_stock_level");
$low_stock = mysqli_fetch_assoc($inv_q)['total'];

$today = date('Y-m-d');
$sched_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM reservations WHERE DATE(start_date) = '$today'");
$today_services = mysqli_fetch_assoc($sched_q)['total'];

$bill_q = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM invoices WHERE status = 'Unpaid'");
$unpaid_total = mysqli_fetch_assoc($bill_q)['total'] ?? 0;

$fleet_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM vehicles WHERE status = 'In Use'");
$active_fleet = mysqli_fetch_assoc($fleet_q)['total'];
?>

<div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: end;">
    <div>
        <h1 style="font-size: 1.8rem; color: var(--deep-navy);">Dashboard Overview</h1>
        <p style="color: var(--text-light);">Operational status for <?= date('l, F d, Y'); ?></p>
    </div>
    
    <?php if($role == 'Administrator'): ?>
        <a href="modules/admin/audit_trail.php" class="btn" style="background: var(--cloud-gray); color: var(--deep-navy);">
            <i class="fas fa-history"></i> View Audit Log
        </a>
    <?php endif; ?>
</div>
 
<?php if (isset($_SESSION['error_msg'])): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid #991b1b;">
        <i class="fas fa-shield-alt"></i> <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
    </div>
<?php endif; ?>
 
<div class="dashboard-grid">
     
    <?php if(in_array($role, ['Administrator', 'Inventory Clerk'])): ?>
    <div class="stat-card" style="border-left-color: <?= $low_stock > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
        <div class="stat-content">
            <h3><?= $low_stock ?></h3>
            <p>Low Stock Items</p>
        </div>
        <div class="stat-icon" style="color: <?= $low_stock > 0 ? 'var(--danger)' : 'var(--success)' ?>; background: #f0f2f5;">
            <i class="fas fa-box-open"></i>
        </div>
    </div>
    <?php endif; ?>

    <!-- FRONT DESK: Admin & Front Desk Staff -->
    <?php if(in_array($role, ['Administrator', 'Front Desk Staff'])): ?>
    <div class="stat-card" style="border-left-color: var(--slate-blue);">
        <div class="stat-content">
            <h3><?= $today_services ?></h3>
            <p>Services Today</p>
        </div>
        <div class="stat-icon"><i class="fas fa-church"></i></div>
    </div>

    <div class="stat-card" style="border-left-color: #f39c12;">
        <div class="stat-content">
            <h3 style="font-size: 1.5rem;">₱<?= number_format($unpaid_total) ?></h3>
            <p>Unpaid Invoices</p>
        </div>
        <div class="stat-icon" style="color: #f39c12;"><i class="fas fa-file-invoice-dollar"></i></div>
    </div>
    <?php endif; ?>
 
    <?php if(in_array($role, ['Administrator', 'Fleet Coordinator'])): ?>
    <div class="stat-card" style="border-left-color: var(--muted-teal);">
        <div class="stat-content">
            <h3><?= $active_fleet ?></h3>
            <p>Vehicles Dispatched</p>
        </div>
        <div class="stat-icon" style="color: var(--muted-teal);"><i class="fas fa-truck"></i></div>
    </div>
    <?php endif; ?>
</div>
 
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
     
    <?php if(in_array($role, ['Administrator', 'Front Desk Staff', 'Fleet Coordinator'])): ?>
    <div class="panel">
        <div class="panel-header">
            <h2><i class="fas fa-calendar-alt"></i> Upcoming Services</h2>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                <?php
                $upcoming = mysqli_query($conn, "SELECT deceased_name, chapel_name, start_date FROM reservations WHERE start_date >= NOW() ORDER BY start_date ASC LIMIT 5");
                if(mysqli_num_rows($upcoming) > 0) {
                    while($row = mysqli_fetch_assoc($upcoming)) {
                        echo "<tr>
                            <td style='padding: 12px; border-bottom: 1px solid #eee;'><strong>{$row['deceased_name']}</strong></td>
                            <td style='padding: 12px; border-bottom: 1px solid #eee; color: #777;'>{$row['chapel_name']}</td>
                            <td style='padding: 12px; border-bottom: 1px solid #eee; text-align: right;'>" . date('M d, H:i', strtotime($row['start_date'])) . "</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='padding: 15px; text-align: center; color: #777;'>No upcoming services scheduled.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
 
    <?php if(in_array($role, ['Administrator', 'Front Desk Staff'])): ?>
    <div class="panel">
        <div class="panel-header">
            <h2><i class="fas fa-clipboard-check"></i> Pending Docs</h2>
        </div>
        <?php
        $pending = mysqli_query($conn, "SELECT d.document_type, r.deceased_name FROM documents d JOIN reservations r ON d.reservation_id = r.id WHERE d.status = 'Pending' LIMIT 5");
        if(mysqli_num_rows($pending) > 0) {
            while($row = mysqli_fetch_assoc($pending)) {
                echo "<div style='padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;'>
                    <span style='font-size: 0.9rem;'>{$row['deceased_name']}</span>
                    <span style='background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem;'>{$row['document_type']}</span>
                </div>";
            }
        } else {
            echo "<div style='text-align: center; color: var(--success); padding: 20px;'><i class='fas fa-check-circle' style='font-size: 2rem;'></i><br>All Docs Verified</div>";
        }
        ?>
    </div>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>