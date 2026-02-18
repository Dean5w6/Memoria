<?php
include('../../includes/header.php');

if (!isset($_GET['id'])) header("Location: tracking.php");
$id = intval($_GET['id']);


$q = mysqli_query($conn, "SELECT * FROM documents WHERE id=$id");
$doc = mysqli_fetch_assoc($q);

if ($doc['status'] == 'Verified') {
    echo "<div class='panel'><h3>Cannot Edit</h3><p>Verified documents cannot be modified.</p><a href='tracking.php' class='btn'>Back</a></div>";
    include('../../includes/footer.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ref = trim($_POST['ref_num']);
    if (!empty($ref)) {
        mysqli_query($conn, "UPDATE documents SET reference_number='$ref' WHERE id=$id");
        $_SESSION['success_msg'] = "Document Reference Updated.";
        header("Location: tracking.php");
        exit();
    }
}
?>

<div class="panel" style="max-width: 500px; margin: 0 auto;">
    <div class="panel-header"><h2>Edit Document</h2></div>
    <form method="POST">
        <div class="form-group">
            <label>Document Type</label>
            <input type="text" class="form-control" value="<?= $doc['document_type'] ?>" disabled>
        </div>
        <div class="form-group">
            <label>Reference Number</label>
            <input type="text" name="ref_num" class="form-control" value="<?= $doc['reference_number'] ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="tracking.php" class="btn">Cancel</a>
    </form>
</div>
<?php include('../../includes/footer.php'); ?>