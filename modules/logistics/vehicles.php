<?php
include('../../includes/header.php');

$error = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_vehicle'])) {
    $name = trim($_POST['name']);
    $plate = trim($_POST['plate']);
    
    if (empty($name) || empty($plate)) {
        $error = "Vehicle Name and Plate Number are required.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO vehicles (vehicle_name, plate_number) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $name, $plate);
        if(mysqli_stmt_execute($stmt)){
            echo "<script>window.location.href='vehicles.php';</script>";
            exit();
        } else {
            $error = "Error: Could not add vehicle (Duplicate plate?).";
        }
    }
}


if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    mysqli_query($conn, "UPDATE vehicles SET status = IF(status='Maintenance', 'Available', 'Maintenance') WHERE id=$id");
    echo "<script>window.location.href='vehicles.php';</script>";
}
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Fleet Management</h1>
        <p style="color: var(--text-light);">Manage hearses and service vehicles.</p>
    </div>
    <button onclick="document.getElementById('addForm').style.display='block'" class="btn btn-primary"><i class="fas fa-car"></i> Add Vehicle</button>
</div>

<?php if($error): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
    </div>
<?php endif; ?>

<!-- Hidden Add Form with novalidate -->
<div id="addForm" class="panel" style="display: none; margin-bottom: 20px; border-left: 5px solid var(--slate-blue);">
    <form method="POST" style="display: flex; gap: 10px; align-items: end;" novalidate>
        <div style="flex: 2;">
            <label>Vehicle Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. White Cadillac">
        </div>
        <div style="flex: 1;">
            <label>Plate Number</label>
            <input type="text" name="plate" class="form-control" placeholder="ABC-123">
        </div>
        <button type="submit" name="add_vehicle" class="btn btn-primary" style="height: 48px;">Save</button>
        <button type="button" class="btn" style="height: 48px; background: #eee;" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
    </form>
</div>

<!-- List of Vehicles -->
<div class="dashboard-grid">
    <?php
    $q = mysqli_query($conn, "SELECT * FROM vehicles");
    while($row = mysqli_fetch_assoc($q)): 
        $statusColor = 'var(--success)'; 
        if($row['status'] == 'In Use') $statusColor = 'var(--slate-blue)';
        if($row['status'] == 'Maintenance') $statusColor = 'var(--danger)';
    ?>
    <div class="stat-card" style="display: block; border-left-color: <?= $statusColor ?>;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div class="stat-icon" style="background: #f0f2f5; color: var(--deep-navy);">
                <i class="fas fa-shuttle-van"></i>
            </div>
            <span style="background: <?= $statusColor ?>; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; text-transform: uppercase;"><?= $row['status'] ?></span>
        </div>
        <h3 style="margin-top: 15px; font-size: 1.2rem;"><?= htmlspecialchars($row['vehicle_name']) ?></h3>
        <p style="margin: 0; color: #777;">Plate: <?= htmlspecialchars($row['plate_number']) ?></p>
        
        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
            <a href="vehicles.php?toggle=<?= $row['id'] ?>" style="color: var(--text-light); font-size: 0.8rem; text-decoration: none;">
                <i class="fas fa-tools"></i> Toggle Maintenance
            </a>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include('../../includes/footer.php'); ?>