<?php
/**
 * Session Management Functions
 * 
 * This file contains functions for managing user sessions,
 * including tracking online users.
 */

/**
 * Update or create a user session record in the database
 * Called during login and page navigation for tracking online users
 */
function update_user_session($conn, $user_id, $session_id) {
    // First check if this is an admin user or regular user
    $is_admin = isset($_SESSION['admin_id']) && !isset($_SESSION['admin_from_user']);
    
    // Skip tracking for admin-only users to avoid foreign key constraint issues
    if ($is_admin) {
        // Only update the last_login time in the administrator table
        $admin_update_sql = "UPDATE administrator SET last_login = NOW() WHERE admin_id = ?";
        $admin_update_stmt = $conn->prepare($admin_update_sql);
        $admin_update_stmt->bind_param("i", $user_id);
        $admin_update_stmt->execute();
        return; // Exit early
    }
    
    // For regular users, first verify the user exists in the users table
    $check_user_sql = "SELECT user_id FROM users WHERE user_id = ?";
    $check_user_stmt = $conn->prepare($check_user_sql);
    $check_user_stmt->bind_param("i", $user_id);
    $check_user_stmt->execute();
    $user_result = $check_user_stmt->get_result();
    
    // Only continue if the user exists
    if ($user_result->num_rows > 0) {
        // Now check if the session already exists
        $check_sql = "SELECT id FROM user_sessions WHERE session_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $session_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        $current_time = date('Y-m-d H:i:s');
        
        if ($result->num_rows > 0) {
            // Session exists, update last_activity
            $update_sql = "UPDATE user_sessions SET last_activity = ? WHERE session_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $current_time, $session_id);
            $update_stmt->execute();
        } else {
            // Session doesn't exist, create new record
            $insert_sql = "INSERT INTO user_sessions (user_id, session_id, last_activity) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iss", $user_id, $session_id, $current_time);
            $insert_stmt->execute();
        }
    }
}

/**
 * Clean old sessions from the database
 * This should be called periodically to remove inactive sessions
 */
function clean_old_sessions($conn, $timeout_minutes = 30) {
    $timeout = date('Y-m-d H:i:s', strtotime("-$timeout_minutes minutes"));
    $delete_sql = "DELETE FROM user_sessions WHERE last_activity < ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $timeout);
    $delete_stmt->execute();
    
    return $delete_stmt->affected_rows;
}

/**
 * Get count of online users
 */
function get_online_users_count($conn, $timeout_minutes = 15) {
    $timeout = date('Y-m-d H:i:s', strtotime("-$timeout_minutes minutes"));
    $sql = "SELECT COUNT(*) as count FROM user_sessions WHERE last_activity > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $timeout);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    return $data['count'];
}
?> 