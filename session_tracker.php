<?php
/**
 * Session Tracker
 * 
 * This file should be included on all pages to track user activity.
 * It updates the user_sessions table to keep track of online users.
 */

// Include required files if not already included
if (!function_exists('update_user_session')) {
    include_once 'session_management.php';
}

if (!isset($conn)) {
    include_once 'db_connection.php';
}

// Only track sessions if a user is logged in
if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) {
    try {
        // Get user ID (prefer regular user, fall back to admin if needed)
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['admin_id'];
        
        // Update session activity
        update_user_session($conn, $user_id, session_id());
        
        // Periodically clean old sessions (1% chance to run on each page load)
        if (mt_rand(1, 100) === 1) {
            clean_old_sessions($conn, 30); // Clean sessions older than 30 minutes
        }
    } catch (Exception $e) {
        // Silently handle errors to prevent page display issues
        error_log("Session tracking error: " . $e->getMessage());
    }
}
?> 