<?php
include('../../includes/header.php');

// --- 1. HANDLE DELETE ACTION ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Secure Delete using Prepared Statement
    $stmt = mysqli_prepare($conn, "DELETE FROM items WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Optional: Add to Audit Log
        if(function_exists('logActivity')) {
            logActivity($conn, $_SESSION['user_id'], "Delete Inventory", "Deleted item ID: $id");
        }
        echo "<script>alert('Item deleted successfully.'); window.location.href='manage.php';</script>";
    } else {
        echo "<script>alert('Error deleting item.');</script>";
    }
    mysqli_stmt_close($stmt);
}

// --- 2. HANDLE CATEGORY FILTER ---
$category = isset($_GET['cat']) ? $_GET['cat'] : 'All';
// Secure filtering logic
if ($category == 'All') {
    $sql = "SELECT * FROM items ORDER BY category, item_name ASC";
    $stmt = mysqli_prepare($conn, $sql);
} else {
    $sql = "SELECT * FROM items WHERE category = ? ORDER BY item_name ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $category);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Inventory Management</h1>
        <p style="color: var(--text-light);">Track caskets, urns, rentals, and supplies.</p>
    </div>
    <a href="add_casket.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Item</a>
</div>

<!-- Category Tabs -->
<div style="margin-bottom: 20px;">
    <?php 
    $cats = ['All', 'Casket', 'Urn', 'Rental', 'Consumable', 'Religious'];
    foreach($cats as $c) {
        $activeStyle = ($category == $c) ? 'background: var(--slate-blue); color: white;' : 'background: white; color: var(--text-dark);';
        echo "<a href='?cat=$c' class='btn' style='margin-right: 5px; border: 1px solid #ddd; $activeStyle'>$c</a>";
    }
    ?>
</div>

<div class="panel">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left; color: var(--deep-navy);">
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Category</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Item Name</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Material/Desc</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Price (PHP)</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Stock Level</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Low Stock Logic
                    $is_low = $row['stock_quantity'] <= $row['min_stock_level'];
                    $stock_badge = $is_low 
                        ? "<span style='background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;'><i class='fas fa-exclamation-triangle'></i> Low: {$row['stock_quantity']}</span>" 
                        : "<span style='background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;'>{$row['stock_quantity']} Units</span>";
                    
                    echo "<tr>
                        <td style='padding: 15px; border-bottom: 1px solid #eee;'><span style='font-size: 0.8rem; background: #e3f2fd; color: #1565c0; padding: 2px 6px; border-radius: 4px;'>{$row['category']}</span></td>
                        <td style='padding: 15px; border-bottom: 1px solid #eee; font-weight: 500;'>{$row['item_name']}</td>
                        <td style='padding: 15px; border-bottom: 1px solid #eee;'>{$row['material']}</td>
                        <td style='padding: 15px; border-bottom: 1px solid #eee;'>₱" . number_format($row['price'], 2) . "</td>
                        <td style='padding: 15px; border-bottom: 1px solid #eee;'>$stock_badge</td>
                        <td style='padding: 15px; border-bottom: 1px solid #eee; text-align: center;'>
                            <!-- Edit Button -->
                            <a href='edit_casket.php?id={$row['id']}' class='btn' style='padding: 6px 10px; font-size: 0.8rem; background: #eee; color: var(--deep-navy); margin-right: 5px;' title='Edit Item'>
                                <i class='fas fa-edit'></i>
                            </a>
                            
                            <!-- DELETE BUTTON -->
                            <a href='manage.php?delete={$row['id']}' 
                               onclick='return confirm(\"Are you sure you want to PERMANENTLY delete this item?\");' 
                               class='btn' 
                               style='padding: 6px 10px; font-size: 0.8rem; background: #fee2e2; color: #991b1b;' 
                               title='Delete Item'>
                                <i class='fas fa-trash'></i>
                            </a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align: center; padding: 30px; color: #777;'>No items found in this category.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('../../includes/footer.php'); ?>