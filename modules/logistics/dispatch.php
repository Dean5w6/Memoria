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
        $error = "All fields are required. Please fill out the dispatch details.";
    } else {
        
        $sql = "INSERT INTO dispatches (reservation_id, vehicle_id, driver_id, location_from, location_to, dispatch_time, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Scheduled')";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiisss", $res_id, $veh_id, $drv_id, $loc_from, $loc_to, $time);
            
            if (mysqli_stmt_execute($stmt)) {
                
                $update_veh = mysqli_prepare($conn, "UPDATE vehicles SET status='In Use' WHERE id = ?");
                mysqli_stmt_bind_param($update_veh, "i", $veh_id);
                mysqli_stmt_execute($update_veh);
                
                
                if(file_exists('../../includes/logger.php')) {
                    require_once('../../includes/logger.php');
                    logActivity($conn, $_SESSION['user_id'], "Fleet Dispatch", "Dispatched Vehicle ID $veh_id to $loc_to");
                }
                
                
                echo "<script>window.location.href='dispatch.php';</script>";
                exit();
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
        }
    }
}


if (isset($_GET['complete'])) {
    $dispatch_id = intval($_GET['complete']);
    $veh_id = intval($_GET['vid']);
    
    
    mysqli_query($conn, "UPDATE dispatches SET status='Completed' WHERE id=$dispatch_id");
    
    
    mysqli_query($conn, "UPDATE vehicles SET status='Available' WHERE id=$veh_id");
    
    echo "<script>window.location.href='dispatch.php';</script>";
    exit();
}
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Dispatch Schedule</h1>
        <p style="color: var(--text-light);">Assign drivers and vehicles to specific locations.</p>
    </div>
</div>

<?php if($error): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
     
    <div class="panel">
        <h3 style="margin-bottom: 20px; color: var(--deep-navy);">New Dispatch Order</h3>
        
        <form method="POST" novalidate>
            <div class="form-group">
                <label>Select Service (Reservation)</label>
                <select name="reservation_id" class="form-control" required>
                    <option value="">-- Choose Upcoming Service --</option>
                    <?php
                    
                    $q = mysqli_query($conn, "SELECT r.id, r.deceased_name, r.start_date FROM reservations r WHERE r.start_date >= CURDATE()");
                    while($row = mysqli_fetch_assoc($q)) {
                        echo "<option value='{$row['id']}'>" . htmlspecialchars($row['deceased_name']) . " (" . date('M d H:i', strtotime($row['start_date'])) . ")</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Assign Vehicle</label>
                <select name="vehicle_id" class="form-control" required>
                    <option value="">-- Available Vehicles --</option>
                    <?php
                    
                    $q = mysqli_query($conn, "SELECT * FROM vehicles WHERE status='Available'");
                    while($row = mysqli_fetch_assoc($q)) {
                        echo "<option value='{$row['id']}'>" . htmlspecialchars($row['vehicle_name']) . " ({$row['plate_number']})</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Assign Driver</label>
                <select name="driver_id" class="form-control" required>
                    <option value="">-- Select Driver --</option>
                    <?php
                    
                    $q = mysqli_query($conn, "SELECT id, full_name FROM users WHERE role='Driver'");
                    while($row = mysqli_fetch_assoc($q)) {
                        echo "<option value='{$row['id']}'>" . htmlspecialchars($row['full_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
 
            <div class="form-group">
                <label>Pick-up Location</label>
                <input type="text" name="loc_from" class="form-control" placeholder="e.g. Taguig District Hospital">
            </div>

            <div class="form-group">
                <label>Drop-off Location</label>
                <input type="text" name="loc_to" class="form-control" placeholder="e.g. Heritage Park Cemetery">
            </div>

            <div class="form-group">
                <label>Dispatch Time</label>
                <input type="datetime-local" name="dispatch_time" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Dispatch Ticket</button>
        </form>
    </div>
 
    <div class="panel">
        <h3 style="margin-bottom: 20px; color: var(--deep-navy);">Active & Scheduled Trips</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th style="padding: 10px; border-bottom: 2px solid #eee;">Time</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eee;">Service Details</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eee;">Route</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eee;">Vehicle/Driver</th>
                    <th style="padding: 10px; border-bottom: 2px solid #eee;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                $q = "SELECT d.id, d.dispatch_time, d.status, d.vehicle_id, d.location_from, d.location_to,
                             r.deceased_name, v.vehicle_name, u.full_name as driver_name 
                      FROM dispatches d
                      JOIN reservations r ON d.reservation_id = r.id
                      JOIN vehicles v ON d.vehicle_id = v.id
                      JOIN users u ON d.driver_id = u.id
                      ORDER BY d.dispatch_time ASC";
                
                $res = mysqli_query($conn, $q);
                
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        
                        $statusColor = ($row['status'] == 'Completed') ? 'green' : 'orange';
                        
                        echo "<tr>
                            <td style='padding: 10px; border-bottom: 1px solid #eee; font-size: 0.9rem; white-space: nowrap;'>" . date('M d H:i', strtotime($row['dispatch_time'])) . "</td>
                            <td style='padding: 10px; border-bottom: 1px solid #eee;'>
                                <strong>" . htmlspecialchars($row['deceased_name']) . "</strong><br>
                                <span style='font-size:0.8rem; color:$statusColor'>{$row['status']}</span>
                            </td>
                            <td style='padding: 10px; border-bottom: 1px solid #eee; font-size: 0.85rem;'>
                                <div style='color:#777;'>From: " . htmlspecialchars($row['location_from']) . "</div>
                                <div style='color:#777;'>To: " . htmlspecialchars($row['location_to']) . "</div>
                            </td>
                            <td style='padding: 10px; border-bottom: 1px solid #eee; font-size: 0.85rem;'>
                                <div><i class='fas fa-car'></i> " . htmlspecialchars($row['vehicle_name']) . "</div>
                                <div style='color: #777;'><i class='fas fa-user'></i> " . htmlspecialchars($row['driver_name']) . "</div>
                            </td>
                            <td style='padding: 10px; border-bottom: 1px solid #eee;'>";
                            
                        
                        if($row['status'] != 'Completed') {
                            echo "<a href='dispatch.php?complete={$row['id']}&vid={$row['vehicle_id']}' class='btn' style='background: var(--success); color: white; padding: 5px 8px; font-size: 0.7rem;' title='Mark as Completed'><i class='fas fa-check'></i> Done</a>";
                        }
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align: center; padding: 20px; color: #777;'>No dispatches found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>