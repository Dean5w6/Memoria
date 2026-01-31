<?php
include('../../includes/header.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $chapel = trim($_POST['chapel']);
    $deceased = trim($_POST['deceased']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $user_id = $_SESSION['user_id'];

    
    if (empty($chapel) || empty($deceased) || empty($start_date) || empty($end_date)) {
        $error = "All fields are required.";
    } elseif ($start_date >= $end_date) {
        $error = "End date must be after the start date.";
    } else {
        
        
        $check_sql = "SELECT id FROM reservations WHERE chapel_name = ? AND ((? < end_date) AND (? > start_date))";
        
        if ($stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $chapel, $start_date, $end_date);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "Conflict Detected: The $chapel is already booked during this time frame.";
            } else {
                
                $insert_sql = "INSERT INTO reservations (chapel_name, deceased_name, start_date, end_date, reserved_by) VALUES (?, ?, ?, ?, ?)";
                
                if ($stmt_ins = mysqli_prepare($conn, $insert_sql)) {
                    mysqli_stmt_bind_param($stmt_ins, "ssssi", $chapel, $deceased, $start_date, $end_date, $user_id);
                    
                    if (mysqli_stmt_execute($stmt_ins)) {
                        
                        if(file_exists('../../includes/logger.php')) {
                            require_once('../../includes/logger.php');
                            logActivity($conn, $_SESSION['user_id'], "New Reservation", "Booked $chapel for $deceased");
                        }
                        

                        echo "<script>window.location.href='calendar.php';</script>";
                        exit();
                    } else {
                        $error = "Database Error: " . mysqli_error($conn);
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <div class="panel-header">
        <h2>Book a Chapel</h2>
        <a href="calendar.php" class="btn" style="background: var(--cloud-gray); color: var(--text-dark);">Cancel</a>
    </div>

    <?php if($error): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
        </div>
    <?php endif; ?>
 
    <form method="POST" novalidate>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Deceased Name</label>
            <input type="text" name="deceased" class="form-control" placeholder="Full Name of Deceased">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select Chapel</label>
            <select name="chapel" class="form-control">
                <option value="">-- Select Chapel --</option>
                <option value="Chapel A (St. Peter)">Chapel A (St. Peter) - Capacity 50</option>
                <option value="Chapel B (St. Mary)">Chapel B (St. Mary) - Capacity 100</option>
                <option value="Chapel C (Grand Hall)">Chapel C (Grand Hall) - Capacity 200</option>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Start Date & Time</label>
                <input type="datetime-local" name="start_date" class="form-control">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">End Date & Time</label>
                <input type="datetime-local" name="end_date" class="form-control">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm Reservation</button>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>