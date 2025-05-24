<?php
session_start();
include 'db_connection.php';

// Update the last_login time to show admin is logged out
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    
    // If this is a regular admin (not a user with admin role)
    if (!isset($_SESSION['admin_from_user'])) {
        $sql = "UPDATE administrator SET last_login = NULL WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
    }
    
    // Also clean up any session records in the user_sessions table
    $session_id = session_id();
    $sql = "DELETE FROM user_sessions WHERE session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
}

// Clear admin session
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_from_user']);

// Destroy the session completely
session_destroy();

// Redirect to admin login
header("Location: admin_login.php");
exit();
?> 