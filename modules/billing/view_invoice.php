<?php
session_start();
require_once('../../config/db.php');

if (!isset($_SESSION['user_id'])) die("Access Denied");
$id = intval($_GET['id']);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_amount'])) {
    $amount = floatval($_POST['pay_amount']);
    $user = $_SESSION['user_id'];
    
    
    mysqli_query($conn, "INSERT INTO payments (invoice_id, amount, received_by) VALUES ($id, $amount, $user)");
    
    
    if(file_exists('../../includes/logger.php')) {
        require_once('../../includes/logger.php');
        logActivity($conn, $_SESSION['user_id'], "Payment Received", "Recorded PHP $amount for Invoice #$id");
    }
    
    
    $inv = mysqli_fetch_assoc(mysqli_query($conn, "SELECT total_amount FROM invoices WHERE id=$id"));
    $paid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payments WHERE invoice_id=$id"))['total'];
    
    
    $new_status = ($paid >= $inv['total_amount']) ? 'Paid' : 'Partial';
    mysqli_query($conn, "UPDATE invoices SET status='$new_status' WHERE id=$id");
    
    header("Location: view_invoice.php?id=$id");
    exit();
}


$inv = mysqli_fetch_assoc(mysqli_query($conn, "SELECT i.*, r.deceased_name, r.chapel_name FROM invoices i JOIN reservations r ON i.reservation_id = r.id WHERE i.id = $id"));
$items = mysqli_query($conn, "SELECT * FROM invoice_items WHERE invoice_id = $id");
$payments = mysqli_query($conn, "SELECT * FROM payments WHERE invoice_id = $id ORDER BY payment_date DESC");


$total_paid = 0;
$paid_history = [];
while($p = mysqli_fetch_assoc($payments)) { $paid_history[] = $p; $total_paid += $p['amount']; }
$balance = $inv['total_amount'] - $total_paid;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice #<?= str_pad($inv['id'], 5, '0', STR_PAD_LEFT) ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #525659; padding: 30px; font-family: 'Poppins', sans-serif; }
        .paper { background: white; max-width: 800px; margin: 20px auto; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,0.5); min-height: 800px; position: relative; }
        .status-stamp { position: absolute; top: 30px; right: 30px; font-size: 2rem; font-weight: bold; color: <?= $balance <= 0 ? 'green' : 'red' ?>; border: 3px solid <?= $balance <= 0 ? 'green' : 'red' ?>; padding: 5px 20px; transform: rotate(-10deg); opacity: 0.6; }
        .nav-bar { max-width: 800px; margin: 0 auto; display: flex; justify-content: flex-start; }
        @media print { body { background: white; padding: 0; } .no-print, .nav-bar { display: none; } .paper { box-shadow: none; margin: 0; width: 100%; max-width: 100%; } }
    </style>
</head>
<body>
 
<div class="nav-bar no-print">
    <a href="reports.php" class="btn" style="background: white; color: #333; margin-bottom: 10px; font-weight: 600;">
        <i class="fas fa-arrow-left"></i> Back to Billing
    </a>
</div>

<div class="paper">
    <div class="status-stamp"><?= strtoupper($inv['status']) ?></div>
     
    <div style="display: flex; justify-content: space-between; margin-bottom: 40px;">
        <div>
            <h1 style="margin: 0; color: var(--deep-navy);">MEMORIA</h1>
            <p>Funeral & Mortuary Services</p>
        </div>
        <div style="text-align: right;">
            <h3>INVOICE #<?= str_pad($inv['id'], 5, '0', STR_PAD_LEFT) ?></h3>
            <p>Date: <?= date('M d, Y', strtotime($inv['created_at'])) ?></p>
        </div>
    </div>
 
    <div style="margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px;">
        <strong>Bill To Family of:</strong><br>
        <span style="font-size: 1.2rem;"><?= $inv['deceased_name'] ?></span><br>
        Service: <?= $inv['chapel_name'] ?>
    </div>
 
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 10px; text-align: left;">Description</th>
                <th style="padding: 10px; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($items)): ?>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= $item['description'] ?></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">₱<?= number_format($item['amount'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
 
    <div style="text-align: right; margin-bottom: 40px;">
        <div style="font-size: 1.1rem;">Grand Total: <strong>₱<?= number_format($inv['total_amount'], 2) ?></strong></div>
        <div style="font-size: 1.1rem; color: green;">Amount Paid: <strong>- ₱<?= number_format($total_paid, 2) ?></strong></div>
        <div style="font-size: 1.5rem; margin-top: 10px; border-top: 2px solid #333; padding-top: 10px;">
            Balance Due: ₱<?= number_format($balance, 2) ?>
        </div>
    </div>
 
    <?php if(!empty($paid_history)): ?>
    <div style="margin-top: 40px;">
        <h4>Payment History</h4>
        <ul style="list-style: none; padding: 0;">
            <?php foreach($paid_history as $ph): ?>
            <li style="border-bottom: 1px solid #eee; padding: 5px 0; display: flex; justify-content: space-between;">
                <span><?= date('M d, Y H:i', strtotime($ph['payment_date'])) ?></span>
                <span>₱<?= number_format($ph['amount'], 2) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
 
    <div class="no-print" style="margin-top: 50px; border-top: 1px dashed #ccc; padding-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
        
        <?php if($balance > 0): ?>
        <form method="POST" style="margin-top: 20px; background: #f0f2f5; padding: 20px; border-radius: 8px;">
            <h4 style="margin-top: 0;">Add Payment / Installment</h4>
            <div style="display: flex; gap: 10px;">
                <input type="number" step="0.01" name="pay_amount" class="form-control" placeholder="Enter Amount" max="<?= $balance ?>" required>
                <button type="submit" class="btn" style="background: green; color: white;">Record Payment</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>