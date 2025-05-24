<?php
/**
 * Standard header include file
 * 
 * This file should be included at the top of all pages that need user session management.
 * It handles session management, database connection, and user tracking.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Determine the base path
$base_path = dirname(__DIR__) . '/';

// Include database connection
if (!isset($conn)) {
    include_once $base_path . 'db_connection.php';
}

// Include session tracker to update user online status
include_once $base_path . 'session_tracker.php';
?> 