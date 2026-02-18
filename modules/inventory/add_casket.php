<?php
include('../../includes/header.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $name = trim($_POST['item_name']);
    $material = trim($_POST['material']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $min_stock = trim($_POST['min_stock']);

    if (empty($name) || empty($price) || $stock === "" || empty($category)) {
        $error = "Category, Name, Price, and Stock are required fields.";
    } else {
        $sql = "INSERT INTO items (category, item_name, material, price, stock_quantity, min_stock_level) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssdis", $category, $name, $material, $price, $stock, $min_stock);
            
            if (mysqli_stmt_execute($stmt)) {
                
                if(file_exists('../../includes/logger.php')) {
                    require_once('../../includes/logger.php');
                    logActivity($conn, $_SESSION['user_id'], "Add Inventory", "Added $category: $name");
                }
                
                
                $_SESSION['success_msg'] = "<strong>Success!</strong> New item '$name' has been added to inventory.";
                
                header("Location: manage.php");
                exit();
            } else {
                $error = "Database Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <div class="panel-header">
        <h2>Add Inventory Item</h2>
        <a href="manage.php" class="btn" style="background: var(--cloud-gray); color: var(--text-dark);">Cancel</a>
    </div>

    <?php if($error): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <!-- Form fields remain the same as previous -->
        <div class="form-group">
            <label>Item Category</label>
            <select name="category" class="form-control" required>
                <option value="Casket">Casket</option>
                <option value="Urn">Urn</option>
                <option value="Rental">Rental Equipment</option>
                <option value="Consumable">Flowers / Candles</option>
                <option value="Religious">Religious Item</option>
            </select>
        </div>
        <div class="form-group">
            <label>Item Name / Model</label>
            <input type="text" name="item_name" class="form-control" placeholder="e.g. Eternal Peace Casket">
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Material / Description</label>
                <input type="text" name="material" class="form-control" placeholder="e.g. Oak Wood or N/A">
            </div>
            <div class="form-group">
                <label>Price (PHP)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00">
            </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Current Stock</label>
                <input type="number" name="stock" class="form-control" value="0">
            </div>
            <div class="form-group">
                <label>Low Stock Alert Level</label>
                <input type="number" name="min_stock" class="form-control" value="5">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Save to Inventory</button>
    </form>
</div>
<?php include('../../includes/footer.php'); ?>