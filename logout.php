<?php
session_start();  // Start the session
include 'db_connection.php';

// Remove user session from database
if (isset($_SESSION['user_id'])) {
    $session_id = session_id();
    
    // Delete the session from the user_sessions table
    $sql = "DELETE FROM user_sessions WHERE session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    
    // Clear user session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['email']);
    unset($_SESSION['first_name']);
    unset($_SESSION['last_name']);
    unset($_SESSION['organization_id']);
    unset($_SESSION['user_type']);
    
    // Completely destroy the session
    session_destroy();
}

// Redirect to home page
header("Location: MindanaoDataExchange.php");
exit();
?>
