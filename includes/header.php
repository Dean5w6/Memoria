<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../config/db.php');

$timeout_duration = 1800; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "auth/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); 

$current_script = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['user_id']) && $current_script != 'login.php') {
    header("Location: " . BASE_URL . "auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memoria | Mortuary Operations</title>
     
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
<header class="main-header"> 
    <a href="<?= BASE_URL ?>dashboard.php" class="brand">
        <img src="<?= BASE_URL ?>assets/img/logo.png" alt="Memoria Logo" class="brand-logo">
        MEMORIA
    </a>
    
    <nav>
        <ul class="nav-links"> 
            <li>
                <a href="<?= BASE_URL ?>dashboard.php" class="<?= $current_script == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>modules/inventory/manage.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'inventory') !== false ? 'active' : '' ?>">
                    <i class="fas fa-box-open"></i> Inventory
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>modules/scheduling/calendar.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'scheduling') !== false ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Schedule
                </a>
            </li>
            <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Manager'): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/logistics/dispatch.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'logistics') !== false ? 'active' : '' ?>">
                    <i class="fas fa-truck"></i> Fleet
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>modules/billing/reports.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'billing') !== false ? 'active' : '' ?>">
                    <i class="fas fa-receipt"></i> Billing
                </a>
            </li> 
            <li>
                <a href="<?= BASE_URL ?>modules/compliance/tracking.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'compliance') !== false ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-check"></i> Compliance
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="user-profile">
        <span style="font-size: 0.9rem; opacity: 0.9;">Hello, <strong><?= htmlspecialchars($_SESSION['full_name']); ?></strong></span>
        <div class="user-avatar">
            <?= strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
        </div> 
        <a href="<?= BASE_URL ?>auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</header>
<?php endif; ?>

<main class="container">