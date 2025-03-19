<?php
session_start();

// Log the logout action
if (isset($_SESSION["admin_id"])) {
    require_once '../config/database.php';
    
    $log_sql = "INSERT INTO admin_login_logs (admin_id, action, ip_address) VALUES (:admin_id, 'logout', :ip)";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->execute([
        ':admin_id' => $_SESSION["admin_id"],
        ':ip' => $_SERVER['REMOTE_ADDR']
    ]);
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header("location: login.php");
exit; 