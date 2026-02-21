<?php
include('../../includes/header.php');
 
$inventory_items = [];
$q = mysqli_query($conn, "SELECT id, item_name, price, category FROM items WHERE stock_quantity > 0 ORDER BY category, item_name ASC");
while ($row = mysqli_fetch_assoc($q)) {
    $inventory_items[] = $row;
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = intval($_POST['reservation_id']);
     
    $descriptions = $_POST['desc'];
    $costs = $_POST['cost'];
    $total = 0;
 
    foreach ($costs as $c) {
        $total += floatval($c);
    }

    if ($total == 0) {
        $error = "Cannot generate a zero value invoice.";
    } else { 
        $sql = "INSERT INTO invoices (reservation_id, total_amount) VALUES ('$reservation_id', '$total')";
        if (mysqli_query($conn, $sql)) {
            $invoice_id = mysqli_insert_id($conn);
 
            for ($i = 0; $i < count($descriptions); $i++) {
                $d = mysqli_real_escape_string($conn, $descriptions[$i]);
                $c = floatval($costs[$i]);
                
                if(!empty($d)) {
                    mysqli_query($conn, "INSERT INTO invoice_items (invoice_id, description, amount) VALUES ('$invoice_id', '$d', '$c')");
                }
            }
 
            if(function_exists('logActivity')) {
                require_once('../../includes/logger.php');
                logActivity($conn, $_SESSION['user_id'], "Generate Invoice", "Created Invoice #$invoice_id");
            }

            echo "<script>window.location.href='reports.php';</script>";
            exit();
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

    <form method="POST" id="invoiceForm"> 
        <div style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Select Client (Reservation)</label>
            <select name="reservation_id" class="form-control" required>
                <option value="">-- Choose a Service --</option>
                <?php
                $q = mysqli_query($conn, "SELECT id, deceased_name, start_date FROM reservations ORDER BY start_date DESC");
                while($row = mysqli_fetch_assoc($q)) {
                    echo "<option value='{$row['id']}'>{$row['deceased_name']} (Service: " . date('M d', strtotime($row['start_date'])) . ")</option>";
                }
                ?>
            </select>
        </div>
 
        <div style="margin-bottom: 10px; font-weight: 600; color: var(--deep-navy);">Billable Items</div>
        
        <div id="items-container"> 
        </div>

        <button type="button" class="btn" style="background: var(--cloud-gray); color: var(--deep-navy); margin-bottom: 20px;" onclick="addItem()">
            <i class="fas fa-plus"></i> Add Item
        </button>
 
        <div style="text-align: right; font-size: 1.2rem; font-weight: 700; color: var(--deep-navy); margin-bottom: 20px;">
            Total: <span id="displayTotal">₱0.00</span>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Generate Invoice</button>
    </form>
</div>

<script> 
document.addEventListener("DOMContentLoaded", function() {
    addItem(); 
});

function addItem() {
    const container = document.getElementById('items-container');
    const div = document.createElement('div');
    div.className = 'item-row';
    div.style.cssText = 'display: grid; grid-template-columns: 1fr 40px 150px 50px; gap: 10px; margin-bottom: 10px; align-items: center;';
     
    let options = '<option value="">-- Select Item from Inventory --</option>';
    let currentCat = '';
    
    inventoryData.forEach(item => { 
        options += `<option value="${item.item_name}" data-price="${item.price}">${item.category}: ${item.item_name}</option>`;
    });

    div.innerHTML = `
        <div class="input-wrapper">
            <select name="desc[]" class="form-control item-select" onchange="updatePrice(this)">
                ${options}
            </select>
            <input type="text" name="desc[]" class="form-control item-text" placeholder="Custom Description" style="display:none;" disabled>
        </div>
        
        <button type="button" class="btn" style="background: #e0e1dd; padding: 5px;" onclick="toggleInputType(this)" title="Switch between Inventory and Custom Text">
            <i class="fas fa-keyboard"></i>
        </button>

        <input type="number" step="0.01" name="cost[]" class="form-control cost-input" placeholder="0.00" required oninput="calcTotal()">
        
        <button type="button" class="btn" style="background: #fee2e2; color: #991b1b;" onclick="this.parentElement.remove(); calcTotal();">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}
 
function updatePrice(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    const row = selectElement.closest('.item-row');
    const costInput = row.querySelector('.cost-input');
    
    if (price) {
        costInput.value = price;
    } else {
        costInput.value = "";  
    }
    calcTotal();
}
 
function toggleInputType(btn) {
    const row = btn.closest('.item-row');
    const select = row.querySelector('.item-select');
    const text = row.querySelector('.item-text');
    
    if (select.style.display === 'none') { 
        select.style.display = 'block';
        select.disabled = false;
        text.style.display = 'none';
        text.disabled = true;
    } else { 
        select.style.display = 'none';
        select.disabled = true;
        text.style.display = 'block';
        text.disabled = false;
        text.focus();
    }
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.cost-input').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('displayTotal').innerText = '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
</script>

<?php include('../../includes/footer.php'); ?>