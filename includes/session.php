<?php
/**
 * Session Management Functions
 * 
 * This file should be included at the beginning of every page that requires 
 * authenticated users. It handles session starting and session tracking.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
if (!isset($conn)) {
    include_once dirname(__DIR__) . '/db_connection.php';
}

/**
 * Update user session activity
 * 
 * This function updates the last_activity timestamp for the current user
 * in the user_sessions table
 */
function update_session_activity() {
    global $conn;
    
    // Only track if this is a logged in user
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $session_id = session_id();
        $current_time = date('Y-m-d H:i:s');
        
        // Check if session exists
        $check_sql = "SELECT id FROM user_sessions WHERE session_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $session_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing session
            $update_sql = "UPDATE user_sessions SET last_activity = ? WHERE session_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ss", $current_time, $session_id);
            $stmt->execute();
        } else {
            // Create new session record
            $insert_sql = "INSERT INTO user_sessions (user_id, session_id, last_activity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iss", $user_id, $session_id, $current_time);
            $stmt->execute();
        }
    }
}

// Update session activity on page load
try {
    update_session_activity();
} catch (Exception $e) {
    // Log error but don't disrupt user experience
    error_log("Session tracking error: " . $e->getMessage());
}
?> 