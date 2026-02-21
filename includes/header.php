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


if (isset($_SESSION['role']) && $current_script != 'login.php') {
    $role = $_SESSION['role'];
    $uri = $_SERVER['REQUEST_URI'];
    $access_denied = false;

    
    if (strpos($uri, '/modules/inventory/') !== false && !in_array($role, ['Administrator', 'Inventory Clerk'])) {
        $access_denied = true;
    }
    elseif (strpos($uri, '/modules/scheduling/') !== false && !in_array($role, ['Administrator', 'Front Desk Staff'])) {
        $access_denied = true;
    }
    elseif (strpos($uri, '/modules/billing/') !== false && !in_array($role, ['Administrator', 'Front Desk Staff'])) {
        $access_denied = true;
    }
    elseif (strpos($uri, '/modules/compliance/') !== false && !in_array($role, ['Administrator', 'Front Desk Staff'])) {
        $access_denied = true;
    }
    elseif (strpos($uri, '/modules/logistics/') !== false && !in_array($role, ['Administrator', 'Fleet Coordinator'])) {
        $access_denied = true;
    }
    elseif ((strpos($uri, '/modules/reports/') !== false || strpos($uri, '/modules/users/') !== false) && $role !== 'Administrator') {
        $access_denied = true;
    }

    
    if ($access_denied) {
        $_SESSION['error_msg'] = "Access Denied: Your role ($role) does not have permission to view that module.";
        header("Location: " . BASE_URL . "dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memoria | Mortuary Operations</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
<header class="main-header"> 
    <a href="<?php echo BASE_URL; ?>dashboard.php" class="brand">
        <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="Memoria Logo" class="brand-logo">
        MEMORIA
    </a>
    
    <nav>
        <ul class="nav-links"> 
             
            <li>
                <a href="<?php echo BASE_URL; ?>dashboard.php" class="<?php echo ($current_script == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>
 
            <?php if (in_array($_SESSION['role'], ['Administrator', 'Inventory Clerk'])): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>modules/inventory/manage.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'inventory') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-box-open"></i> Inventory
                </a>
            </li>
            <?php endif; ?>
 
            <?php if (in_array($_SESSION['role'], ['Administrator', 'Front Desk Staff'])): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>modules/scheduling/calendar.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'scheduling') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i> Schedule
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>modules/billing/reports.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'billing') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-receipt"></i> Billing
                </a>
            </li> 
            <li>
                <a href="<?php echo BASE_URL; ?>modules/compliance/tracking.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'compliance') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check"></i> Compliance
                </a>
            </li>
            <?php endif; ?>
 
            <?php if (in_array($_SESSION['role'], ['Administrator', 'Fleet Coordinator'])): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>modules/logistics/dispatch.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'logistics') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-truck"></i> Fleet
                </a>
            </li>
            <?php endif; ?>
 
            <?php if ($_SESSION['role'] === 'Administrator'): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>modules/reports/sales_report.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'reports') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>modules/users/manage_users.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'users') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users-cog"></i> Users
                </a>
            </li>
            <?php endif; ?>

        </ul>
    </nav>

    <div class="user-profile">
        <span style="font-size: 0.9rem; opacity: 0.9;">Hello, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong><br><small><?php echo $_SESSION['role']; ?></small></span>
        <div class="user-avatar">
            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
        </div> 
        <a href="<?php echo BASE_URL; ?>auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</header>
<?php endif; ?>

<main class="container">