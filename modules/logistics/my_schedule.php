<?php
include('../../includes/header.php');
 
if (!in_array($_SESSION['role'], ['Driver', 'Administrator'])) {
    die("<div class='panel'>Access Denied</div>");
}

$driver_id = $_SESSION['user_id'];
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">My Upcoming Schedule</h1>
        <p style="color: var(--text-light);">Your assigned dispatches for the coming days.</p>
    </div>
</div>

<div class="panel">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa;">
                <th style="padding: 12px; text-align: left;">Date & Time</th>
                <th style="padding: 12px; text-align: left;">Client</th>
                <th style="padding: 12px; text-align: left;">Vehicle</th>
                <th style="padding: 12px; text-align: left;">Route</th>
                <th style="padding: 12px; text-align: left;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT d.*, r.deceased_name, v.vehicle_name 
                    FROM dispatches d 
                    JOIN reservations r ON d.reservation_id = r.id
                    JOIN vehicles v ON d.vehicle_id = v.id
                    WHERE d.driver_id = ? AND d.status != 'Completed'
                    ORDER BY d.dispatch_time ASC";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $driver_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td style='padding: 12px; border-bottom: 1px solid #eee; font-weight: 600;'>" . date('M d, Y H:i', strtotime($row['dispatch_time'])) . "</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>{$row['deceased_name']}</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>{$row['vehicle_name']}</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>From: {$row['location_from']}<br>To: {$row['location_to']}</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'><span style='background: orange; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;'>{$row['status']}</span></td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align: center; padding: 30px;'>No upcoming trips assigned.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('../../includes/footer.php'); ?>