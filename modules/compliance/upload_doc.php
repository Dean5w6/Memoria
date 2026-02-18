<?php
include('../../includes/header.php');
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $doc_type = $_POST['doc_type'];
    $ref_num = trim($_POST['ref_num']);

    
    $target_dir = "../../uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true); 
    
    $file_name = basename($_FILES["doc_file"]["name"]);
    $file_path = "";
    
    if (!empty($file_name)) {
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_name = uniqid() . "." . $file_ext; 
        $target_file = $target_dir . $new_name;
        
        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
            if (move_uploaded_file($_FILES["doc_file"]["tmp_name"], $target_file)) {
                $file_path = $new_name;
            } else {
                $error = "Failed to upload file.";
            }
        } else {
            $error = "Only JPG, PNG, and PDF files are allowed.";
        }
    }

    if (empty($error)) {
        if (empty($reservation_id) || empty($doc_type) || empty($ref_num)) {
            $error = "Please fill in all text fields.";
        } else {
            
            $sql = "INSERT INTO documents (reservation_id, document_type, reference_number, file_path, status) VALUES (?, ?, ?, ?, 'Pending')";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "isss", $reservation_id, $doc_type, $ref_num, $file_path);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_msg'] = "Document uploaded successfully!";
                    header("Location: tracking.php");
                    exit();
                } else {
                    $error = "Database Error.";
                }
            }
        }
    }
}
?>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <div class="panel-header"><h2>Register Document</h2></div>
    <?php if($error): ?><div style="background:#fee2e2;color:#991b1b;padding:10px;margin-bottom:20px;"><?= $error ?></div><?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label>Client</label>
            <select name="reservation_id" class="form-control" required>
                <option value="">-- Select Client --</option>
                <?php
                $q = mysqli_query($conn, "SELECT id, deceased_name FROM reservations WHERE end_date >= CURDATE()");
                while($row = mysqli_fetch_assoc($q)) { echo "<option value='{$row['id']}'>{$row['deceased_name']}</option>"; }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="doc_type" class="form-control" required>
                <option value="Death Certificate">Death Certificate</option>
                <option value="Burial Permit">Burial Permit</option>
                <option value="Transfer Permit">Transfer Permit</option>
            </select>
        </div>
        <div class="form-group">
            <label>Reference #</label>
            <input type="text" name="ref_num" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Attach Scan/Image (JPG, PNG, PDF)</label>
            <input type="file" name="doc_file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Upload & Record</button>
    </form>
</div>
<?php include('../../includes/footer.php'); ?>