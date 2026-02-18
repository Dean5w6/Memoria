<?php
include('../../includes/header.php');

// Default to current month
$start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-t');

// Query Sales
$sql = "SELECT p.*, i.id as inv_id, r.deceased_name, u.full_name as staff 
        FROM payments p 
        JOIN invoices i ON p.invoice_id = i.id 
        JOIN reservations r ON i.reservation_id = r.id
        LEFT JOIN users u ON p.received_by = u.id
        WHERE DATE(p.payment_date) BETWEEN '$start' AND '$end' 
        ORDER BY p.payment_date DESC";
$result = mysqli_query($conn, $sql);

// Calculate Total
$total_sales = 0;
?>

<div class="panel-header">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Financial Report</h1>
        <p style="color: var(--text-light);">Sales and collections summary.</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Report</button>
</div>

<div class="panel no-print">
    <form method="GET" style="display: flex; gap: 10px; align-items: end;">
        <div>
            <label>From Date</label>
            <input type="date" name="start" value="<?= $start ?>" class="form-control">
        </div>
        <div>
            <label>To Date</label>
            <input type="date" name="end" value="<?= $end ?>" class="form-control">
        </div>
        <button type="submit" class="btn" style="background: var(--slate-blue); color: white; height: 45px;">Filter</button>
    </form>
</div>

<div class="panel">
    <h3 style="text-align: center; margin-bottom: 20px;">Sales Report (<?= date('M d', strtotime($start)) ?> - <?= date('M d', strtotime($end)) ?>)</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #333;">
                <th style="padding: 10px; text-align: left;">Date</th>
                <th style="padding: 10px; text-align: left;">Invoice #</th>
                <th style="padding: 10px; text-align: left;">Client</th>
                <th style="padding: 10px; text-align: left;">Received By</th>
                <th style="padding: 10px; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $total_sales += $row['amount'];
                    echo "<tr>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>" . date('Y-m-d H:i', strtotime($row['payment_date'])) . "</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>INV-" . str_pad($row['inv_id'], 5, '0', STR_PAD_LEFT) . "</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$row['deceased_name']}</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$row['staff']}</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($row['amount'], 2) . "</td>
                    </tr>";
                }
                echo "<tr style='font-weight: bold; background: #f8f9fa;'>
                    <td colspan='4' style='padding: 15px; text-align: right;'>TOTAL COLLECTIONS:</td>
                    <td style='padding: 15px; text-align: right; color: green;'>₱" . number_format($total_sales, 2) . "</td>
                </tr>";
            } else {
                echo "<tr><td colspan='5' style='text-align: center; padding: 20px;'>No sales found for this period.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<style>
    @media print {
        .main-header, .no-print { display: none; }
        body { background: white; }
        .panel { box-shadow: none; border: none; }
    }
</style>

<?php include('../../includes/footer.php'); ?>
