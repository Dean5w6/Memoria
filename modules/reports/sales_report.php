<?php
include('../../includes/header.php');
 
if ($_SESSION['role'] !== 'Administrator') {
    die("<div class='panel'><h2>Access Denied</h2><p>Only Administrators can view Financial Reports.</p></div>");
}
 
$start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-t');
 
$sql = "SELECT p.*, i.id as inv_id, r.deceased_name, u.full_name as staff 
        FROM payments p 
        JOIN invoices i ON p.invoice_id = i.id 
        JOIN reservations r ON i.reservation_id = r.id
        LEFT JOIN users u ON p.received_by = u.id
        WHERE DATE(p.payment_date) BETWEEN ? AND ? 
        ORDER BY p.payment_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $start, $end);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total_sales = 0;
?>

<div class="panel-header">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Financial Report</h1>
        <p style="color: var(--text-light);">Sales and collections summary.</p>
    </div>
     
    <a href="generate_pdf.php?start=<?= htmlspecialchars($start) ?>&end=<?= htmlspecialchars($end) ?>" target="_blank" class="btn btn-primary">
        <i class="fas fa-file-pdf"></i> Generate PDF
    </a>
</div>
 
<div class="panel no-print">
    <form method="GET" style="display: flex; gap: 10px; align-items: end;">
        <div>
            <label>From Date</label>
            <input type="date" name="start" value="<?= htmlspecialchars($start) ?>" class="form-control">
        </div>
        <div>
            <label>To Date</label>
            <input type="date" name="end" value="<?= htmlspecialchars($end) ?>" class="form-control">
        </div>
        <button type="submit" class="btn" style="background: var(--slate-blue); color: white; height: 45px;">Filter</button>
    </form>
</div>
 
<div class="panel">
    <div class="print-header" style="text-align: center; margin-bottom: 20px; display: none;">
        <h2>Memoria System - Sales Report</h2>
        <h4>Period: <?= date('M d, Y', strtotime($start)) ?> to <?= date('M d, Y', strtotime($end)) ?></h4>
    </div>
    
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
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($row['deceased_name']) . "</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($row['staff']) . "</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($row['amount'], 2) . "</td>
                    </tr>";
                }
                echo "<tr style='font-weight: bold; background: #f8f9fa;'>
                    <td colspan='4' style='padding: 15px; text-align: right; border-top: 2px solid #333;'>TOTAL COLLECTIONS:</td>
                    <td style='padding: 15px; text-align: right; color: green; border-top: 2px solid #333;'>₱" . number_format($total_sales, 2) . "</td>
                </tr>";
            } else {
                echo "<tr><td colspan='5' style='text-align: center; padding: 20px;'>No sales data found for the selected period.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('../../includes/footer.php'); ?>