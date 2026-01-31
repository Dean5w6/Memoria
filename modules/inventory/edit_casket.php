<?php
include('../../includes/header.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage.php");
    exit();
}

$id = intval($_GET['id']);
$error = "";


$stmt = mysqli_prepare($conn, "SELECT * FROM items WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$item) {
    echo "<script>window.location.href='manage.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    
    if ($price === "" || $stock === "") {
        $error = "Price and Stock are required.";
    } else {
        $sql = "UPDATE items SET price = ?, stock_quantity = ? WHERE id = ?";
        if ($update_stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($update_stmt, "dii", $price, $stock, $id);
            if (mysqli_stmt_execute($update_stmt)) {
                echo "<script>window.location.href='manage.php';</script>";
                exit();
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="panel" style="max-width: 500px; margin: 0 auto;">
    <div class="panel-header">
        <h2>Update Item: <?= htmlspecialchars($item['item_name']); ?></h2>
        <a href="manage.php" class="btn" style="background: var(--cloud-gray); color: var(--text-dark);">Cancel</a>
    </div>

    <?php if($error): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Category</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($item['category']); ?>" disabled style="background: #eee;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Current Stock Level</label>
            <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($item['stock_quantity']); ?>">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Price (PHP)</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($item['price']); ?>">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Update Record</button>
    </form>
</div>

<?php include('../../includes/footer.php'); ?>