<?php
include('../../includes/header.php');

if (isset($_GET['pay'])) {
    $inv_id = intval($_GET['pay']);
    mysqli_query($conn, "UPDATE invoices SET status='Paid' WHERE id=$inv_id");
    echo "<script>window.location.href='reports.php';</script>";
}
?>

<div class="panel-header" style="border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Billing & Invoices</h1>
        <p style="color: var(--text-light);">Track payments and generate statements.</p>
    </div>
    <a href="create_invoice.php" class="btn btn-primary"><i class="fas fa-file-invoice-dollar"></i> Create New Invoice</a>
</div>

<div class="panel">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left; color: var(--deep-navy);">
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Invoice #</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Client (Deceased)</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Date</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Total Amount</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Status</th>
                <th style="padding: 12px; border-bottom: 2px solid #eee;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT i.*, r.deceased_name 
                      FROM invoices i 
                      JOIN reservations r ON i.reservation_id = r.id 
                      ORDER BY i.created_at DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    
                    $statusColor = '#fee2e2'; 
                    $statusText = '#991b1b';
                    
                    if ($row['status'] == 'Paid') {
                        $statusColor = '#dcfce7'; 
                        $statusText = '#166534';
                    } elseif ($row['status'] == 'Partial') {
                        $statusColor = '#fff3cd'; 
                        $statusText = '#856404';
                    } else if ($row['status'] == 'Cancelled') {
                        $statusColor = '#e2e3e5';
                        $statusText = '#383d41';
                    }
                    
                    echo "<tr>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>INV-" . str_pad($row['id'], 5, '0', STR_PAD_LEFT) . "</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>{$row['deceased_name']}</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee; color: #777;'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee; font-weight: 600;'>₱" . number_format($row['total_amount'], 2) . "</td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>
                            <span style='background: $statusColor; color: $statusText; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;'>{$row['status']}</span>
                        </td>
                        <td style='padding: 12px; border-bottom: 1px solid #eee;'>
                            <!-- REMOVED target='_blank' HERE -->
                            <a href='view_invoice.php?id={$row['id']}' class='btn' style='background: var(--cloud-gray); color: var(--deep-navy); padding: 5px 10px; font-size: 0.8rem;'>
                                <i class='fas fa-eye'></i> View / Pay
                            </a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='padding: 20px; text-align: center; color: #777;'>No invoices found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('../../includes/footer.php'); ?>