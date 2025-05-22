<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    // Store the admin_from_user flag before clearing admin session variables
    $admin_from_user = isset($_SESSION['admin_from_user']) && $_SESSION['admin_from_user'];
    $user_id = null;
    
    // If admin is also a regular user (admin_from_user is set)
    if ($admin_from_user) {
        // This admin is also a regular user, so we'll preserve their user session
        $user_id = $_SESSION['admin_id']; // The admin_id is actually the user_id for these admins
    }
    
    // Clear admin session variables
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_from_user']);
    
    // If this admin is also a regular user, restore their user session
    if ($user_id) {
        // Get user data to recreate user session
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            // Set user session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['organization_id'] = $user['organization_id'];
            
            // Redirect to logged-in home page
            header("Location: HomeLogin.php");
            exit();
        }
    }
}

// Default redirect to main page (not logged in)
header("Location: MindanaoDataExchange.php");
exit();
?> 