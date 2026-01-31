<?php
function logActivity($conn, $user_id, $action, $details) {
    // Get IP Address
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Fix: Convert IPv6 localhost to IPv4 for better readability
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }
    
    $sql = "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "isss", $user_id, $action, $details, $ip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>