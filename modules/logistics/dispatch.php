<?php
include('../../includes/header.php');
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $res_id = $_POST['reservation_id'];
    $veh_id = $_POST['vehicle_id'];
    $drv_id = $_POST['driver_id'];
    $loc_from = trim($_POST['loc_from']);
    $loc_to = trim($_POST['loc_to']);
    $time = $_POST['dispatch_time'];

    if (empty($res_id) || empty($veh_id) || empty($drv_id) || empty($loc_from) || empty($loc_to) || empty($time)) {
        $error = "All fields are required.";
    } else {
        $sql = "INSERT INTO dispatches (reservation_id, vehicle_id, driver_id, location_from, location_to, dispatch_time, status) VALUES (?, ?, ?, ?, ?, ?, 'Scheduled')";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiisss", $res_id, $veh_id, $drv_id, $loc_from, $loc_to, $time);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_query($conn, "UPDATE vehicles SET status='In Use' WHERE id = $veh_id");
                
                
                if(file_exists('../../includes/logger.php')) { require_once('../../includes/logger.php'); logActivity($conn, $_SESSION['user_id'], "Fleet Dispatch", "Dispatched Vehicle ID $veh_id"); }
                
                $_SESSION['success_msg'] = "Dispatch ticket created successfully.";
                header("Location: dispatch.php");
                exit();
            }
        }
    }
}


if (isset($_GET['complete'])) {
    $dispatch_id = intval($_GET['complete']);
    $veh_id = intval($_GET['vid']);
    mysqli_query($conn, "UPDATE dispatches SET status='Completed' WHERE id=$dispatch_id");
    mysqli_query($conn, "UPDATE vehicles SET status='Available' WHERE id=$veh_id");
    
    $_SESSION['success_msg'] = "Dispatch #$dispatch_id marked as Completed. Vehicle is now available.";
    header("Location: dispatch.php");
    exit();
}
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Dispatch Schedule</h1>
        <p style="color: var(--text-light);">Assign drivers and vehicles to specific locations.</p>
    </div>
</div>
 
<?php if (isset($_SESSION['success_msg'])): ?>
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid #166534;">
        <i class="fas fa-check-circle"></i> <?= $_SESSION['success_msg']; ?>
    </div>
    <?php unset($_SESSION['success_msg']); ?>
<?php endif; ?>

<?php if($error): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
    </div>
<?php endif; ?>
 
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <div class="panel">
        <h3 style="margin-bottom: 20px; color: var(--deep-navy);">New Dispatch Order</h3>
        <form method="POST" novalidate> 
             <div class="form-group"><label>Select Service</label><select name="reservation_id" class="form-control" required><option value="">-- Choose Service --</option><?php $q = mysqli_query($conn, "SELECT r.id, r.deceased_name, r.start_date FROM reservations r WHERE r.start_date >= CURDATE()"); while($row = mysqli_fetch_assoc($q)) { echo "<option value='{$row['id']}'>{$row['deceased_name']} (" . date('M d H:i', strtotime($row['start_date'])) . ")</option>"; } ?></select></div>
             <div class="form-group"><label>Vehicle</label><select name="vehicle_id" class="form-control" required><option value="">-- Available Vehicles --</option><?php $q = mysqli_query($conn, "SELECT * FROM vehicles WHERE status='Available'"); while($row = mysqli_fetch_assoc($q)) { echo "<option value='{$row['id']}'>{$row['vehicle_name']} ({$row['plate_number']})</option>"; } ?></select></div>
             <div class="form-group"><label>Driver</label><select name="driver_id" class="form-control" required><option value="">-- Select Driver --</option><?php $q = mysqli_query($conn, "SELECT id, full_name FROM users WHERE role='Driver'"); while($row = mysqli_fetch_assoc($q)) { echo "<option value='{$row['id']}'>{$row['full_name']}</option>"; } ?></select></div>
             <div class="form-group"><label>From</label><input type="text" name="loc_from" class="form-control"></div>
             <div class="form-group"><label>To</label><input type="text" name="loc_to" class="form-control"></div>
             <div class="form-group"><label>Time</label><input type="datetime-local" name="dispatch_time" class="form-control"></div>
             <button type="submit" class="btn btn-primary" style="width: 100%;">Create Dispatch</button>
        </form>
    </div> 
    <div class="panel">
        <h3 style="margin-bottom: 20px;">Active Trips</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead><tr style="background:#f8f9fa; text-align:left;"><th style="padding:10px;">Time</th><th style="padding:10px;">Details</th><th style="padding:10px;">Status</th><th style="padding:10px;">Action</th></tr></thead>
            <tbody>
                <?php
                $q = "SELECT d.id, d.dispatch_time, d.status, d.vehicle_id, r.deceased_name, v.vehicle_name, u.full_name FROM dispatches d JOIN reservations r ON d.reservation_id=r.id JOIN vehicles v ON d.vehicle_id=v.id JOIN users u ON d.driver_id=u.id ORDER BY d.dispatch_time ASC";
                $res = mysqli_query($conn, $q);
                while($row=mysqli_fetch_assoc($res)){
                    echo "<tr><td style='padding:10px;'>".date('M d H:i', strtotime($row['dispatch_time']))."</td><td style='padding:10px;'>{$row['deceased_name']}<br><small>{$row['vehicle_name']}</small></td><td style='padding:10px;'>{$row['status']}</td><td style='padding:10px;'>";
                    if($row['status']!='Completed') echo "<a href='dispatch.php?complete={$row['id']}&vid={$row['vehicle_id']}' class='btn' style='background:green;color:white;padding:5px 8px;font-size:0.7rem;'>Done</a>";
                    echo "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>