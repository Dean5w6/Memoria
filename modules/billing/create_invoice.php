<?php
include('../../includes/header.php');
$error = "";
 
$inventory_items = [];
$q = mysqli_query($conn, "SELECT id, item_name, price, category FROM items WHERE stock_quantity > 0 ORDER BY category, item_name ASC");
while ($row = mysqli_fetch_assoc($q)) {
    $inventory_items[] = $row;
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = intval($_POST['reservation_id']);
    $total = 0;
 
    if (isset($_POST['service_costs'])) {
        foreach ($_POST['service_costs'] as $sc) {
            $total += floatval($sc);
        }
    }
    if (isset($_POST['cost'])) {
        foreach ($_POST['cost'] as $c) {
            $total += floatval($c);
        }
    }

    if (empty($reservation_id) || $total == 0) {
        $error = "Please select a client and add at least one billable item or service.";
    } else {
        $sql = "INSERT INTO invoices (reservation_id, total_amount) VALUES (?, ?)";
        if ($stmt_inv = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt_inv, "id", $reservation_id, $total);
            if (mysqli_stmt_execute($stmt_inv)) {
                $invoice_id = mysqli_insert_id($conn);
                 
                $service_names = ['Chapel Rental', 'Hearse Service', 'Other Fees'];
                for($i = 0; $i < count($_POST['service_costs']); $i++){
                    if(!empty($_POST['service_costs'][$i])){
                         mysqli_query($conn, "INSERT INTO invoice_items (invoice_id, description, amount) VALUES ($invoice_id, '{$service_names[$i]}', '{$_POST['service_costs'][$i]}')");
                    }
                }
                 
                for ($i = 0; $i < count($_POST['desc']); $i++) {
                    if(!empty($_POST['desc'][$i])) {
                         mysqli_query($conn, "INSERT INTO invoice_items (invoice_id, description, amount) VALUES ($invoice_id, '{$_POST['desc'][$i]}', '{$_POST['cost'][$i]}')");
                    }
                }

                if(function_exists('logActivity')) { require_once('../../includes/logger.php'); logActivity($conn, $_SESSION['user_id'], "Generate Invoice", "Created Invoice #$invoice_id"); }
                $_SESSION['success_msg'] = "Invoice #$invoice_id created successfully.";
                header("Location: reports.php");
                exit();
            }
        }
    }
}
?>
<script>
    const inventoryData = <?= json_encode($inventory_items); ?>;
</script>

<div class="panel" style="max-width: 800px; margin: 0 auto;">
    <div class="panel-header">
        <h2>Generate New Invoice</h2>
        <a href="reports.php" class="btn" style="background: var(--cloud-gray); color: var(--text-dark);">Cancel</a>
    </div>

    <?php if($error): ?><div style="background:#fee2e2;color:#991b1b;padding:15px;border-radius:6px;margin-bottom:15px;"><?= $error ?></div><?php endif; ?>

    <form method="POST" id="invoiceForm" novalidate>
        <div class="form-group">
            <label style="font-weight: 600;">Select Client (Reservation)</label>
            <select name="reservation_id" class="form-control" required>
                <option value="">-- Choose a Service --</option>
                <?php
                $q = mysqli_query($conn, "SELECT id, deceased_name, start_date FROM reservations WHERE id NOT IN (SELECT reservation_id FROM invoices) ORDER BY start_date DESC");
                while($row = mysqli_fetch_assoc($q)) {
                    echo "<option value='{$row['id']}'>{$row['deceased_name']} (Service: " . date('M d', strtotime($row['start_date'])) . ")</option>";
                }
                ?>
            </select>
        </div>
 
        <div style="margin-bottom: 25px; border-top: 1px solid #eee; padding-top: 20px;">
            <div style="font-weight: 600; color: var(--deep-navy); margin-bottom: 10px;">Standard Service Fees</div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label>Chapel Rental</label>
                    <input type="number" name="service_costs[]" step="0.01" class="form-control service-cost" placeholder="0.00" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label>Hearse Service</label>
                    <input type="number" name="service_costs[]" step="0.01" class="form-control service-cost" placeholder="0.00" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label>Other Service Fees</label>
                    <input type="number" name="service_costs[]" step="0.01" class="form-control service-cost" placeholder="0.00" oninput="calcTotal()">
                </div>
            </div>
        </div>

        <div style="margin-bottom: 10px; font-weight: 600; color: var(--deep-navy); border-top: 1px solid #eee; padding-top: 20px;">Billable Items</div>
        <div id="items-container"></div>

        <button type="button" class="btn" style="background: var(--cloud-gray); color: var(--deep-navy); margin-bottom: 20px;" onclick="addItem()">
            <i class="fas fa-plus"></i> Add Another Item
        </button>

        <div style="text-align: right; font-size: 1.2rem; font-weight: 700; color: var(--deep-navy); margin-bottom: 20px;">
            Total: <span id="displayTotal">₱0.00</span>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Generate Invoice</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() { addItem(); }); // Load one row on start

function addItem() {
    const container = document.getElementById('items-container');
    const div = document.createElement('div');
    div.className = 'item-row';
    div.style.cssText = 'display: grid; grid-template-columns: 1fr 40px 150px 50px; gap: 10px; margin-bottom: 10px; align-items: center;';
    let options = '<option value="">-- Select from Inventory --</option>';
    inventoryData.forEach(item => { options += `<option value="${item.item_name}" data-price="${item.price}">${item.category}: ${item.item_name}</option>`; });
    div.innerHTML = `<div><select name="desc[]" class="form-control item-select" onchange="updatePrice(this)">${options}</select><input type="text" name="desc[]" class="form-control item-text" placeholder="Custom Desc" style="display:none;" disabled></div><button type="button" class="btn" onclick="toggleInputType(this)"><i class="fas fa-keyboard"></i></button><input type="number" step="0.01" name="cost[]" class="form-control cost-input" oninput="calcTotal()"><button type="button" class="btn" style="background:#fee2e2;color:#991b1b;" onclick="this.parentElement.remove(); calcTotal();"><i class="fas fa-times"></i></button>`;
    container.appendChild(div);
}

function updatePrice(select) {
    const price = select.options[select.selectedIndex].getAttribute('data-price');
    const costInput = select.closest('.item-row').querySelector('.cost-input');
    costInput.value = price || "";
    calcTotal();
}

function toggleInputType(btn) {
    const row = btn.closest('.item-row');
    const select = row.querySelector('.item-select');
    const text = row.querySelector('.item-text');
    if (select.style.display === 'none') {
        select.style.display = 'block'; select.disabled = false; text.style.display = 'none'; text.disabled = true;
    } else {
        select.style.display = 'none'; select.disabled = true; text.style.display = 'block'; text.disabled = false; text.focus();
    }
}

function calcTotal() {
    let total = 0; 
    document.querySelectorAll('.service-cost').forEach(input => { total += parseFloat(input.value) || 0; });
    document.querySelectorAll('.cost-input').forEach(input => { total += parseFloat(input.value) || 0; });
    document.getElementById('displayTotal').innerText = '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
</script>

<?php include('../../includes/footer.php'); ?>