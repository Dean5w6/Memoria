<?php
include('../../includes/header.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $doc_type = $_POST['doc_type'];
    $ref_num = trim($_POST['ref_num']);
 
    if (empty($reservation_id) || empty($doc_type) || empty($ref_num)) {
        $error = "Please fill in all fields.";
    } else {
        $sql = "INSERT INTO documents (reservation_id, document_type, reference_number, status) VALUES (?, ?, ?, 'Pending')";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iss", $reservation_id, $doc_type, $ref_num);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>window.location.href='tracking.php';</script>";
                exit();
            } else {
                $error = "Error recording document.";
            }
        }
    }
}
?>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <div class="panel-header">
        <h2>Register Document</h2>
        <a href="tracking.php" class="btn" style="background: var(--cloud-gray); color: var(--text-dark);">Cancel</a>
    </div>

    <?php if($error): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="form-group">
            <label>Select Client Service</label>
            <select name="reservation_id" class="form-control">
                <option value="">-- Select Client --</option>
                <?php
                $q = mysqli_query($conn, "SELECT id, deceased_name FROM reservations WHERE end_date >= CURDATE()");
                while($row = mysqli_fetch_assoc($q)) {
                    echo "<option value='{$row['id']}'>" . htmlspecialchars($row['deceased_name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Document Type</label>
            <select name="doc_type" class="form-control">
                <option value="">-- Select Type --</option>
                <option value="Death Certificate">Death Certificate</option>
                <option value="Burial Permit">Burial Permit</option>
                <option value="Transfer Permit">Transfer Permit</option>
            </select>
        </div>

        <div class="form-group">
            <label>Reference / Serial Number</label>
            <input type="text" name="ref_num" class="form-control" placeholder="e.g. REG-2025-001">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Record Document</button>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>