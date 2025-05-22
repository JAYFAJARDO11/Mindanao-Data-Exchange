<?php
/**
 * Security Check Utility
 * Include this file in pages that require authentication and access control
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function is_user_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 * @return bool True if admin is logged in, false otherwise
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

/**
 * Check if user has an organization
 * @return bool True if user has an organization, false otherwise
 */
function user_has_organization() {
    return isset($_SESSION['organization_id']) && $_SESSION['organization_id'] !== null;
}

/**
 * Redirect to login page if user is not logged in
 * @param string $message Optional error message to display
 */
function require_login($message = "Please log in to access this page.") {
    if (!is_user_logged_in()) {
        $_SESSION['error_message'] = $message;
        header("Location: login.php");
        exit();
    }
}

/**
 * Redirect to admin login page if admin is not logged in
 * @param string $message Optional error message to display
 */
function require_admin_login($message = "Please log in as an administrator to access this page.") {
    if (!is_admin_logged_in()) {
        $_SESSION['error_message'] = $message;
        header("Location: admin_login.php");
        exit();
    }
}

/**
 * Redirect to unauthorized page if user doesn't have an organization
 * @param string $message Optional error message to display
 */
function require_organization($message = "You must belong to an organization to access this feature.") {
    if (!user_has_organization()) {
        $_SESSION['error_message'] = $message;
        header("Location: unauthorized.php");
        exit();
    }
}

/**
 * Require a specific session flag to be set
 * @param string $flag The session flag to check
 * @param mixed $value The expected value of the flag
 * @param string $redirect_url Where to redirect if check fails
 * @param string $message Optional error message to display
 * @param string $error_prefix Optional prefix for error_type to avoid conflicts
 */
function require_session_flag($flag, $value, $redirect_url, $message = "Access denied.", $error_prefix = "") {
    if (!isset($_SESSION[$flag]) || $_SESSION[$flag] !== $value) {
        $_SESSION['error_message'] = $message;
        // Use a prefixed error type to avoid conflicts between different security checks
        if (!empty($error_prefix)) {
            $_SESSION['error_type'] = $error_prefix . "_permission";
        } else {
            $_SESSION['error_type'] = "permission";
        }
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Check if the current request is a direct URL access (not from a form submission or legitimate flow)
 * @param array $valid_referrers Array of valid referring pages
 * @return bool True if direct access, false otherwise
 */
function is_direct_access($valid_referrers = []) {
    // If no referrer or not in the list of valid referrers
    if (!isset($_SERVER['HTTP_REFERER']) || (
        !empty($valid_referrers) && 
        !in_array(basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH)), $valid_referrers)
    )) {
        return true;
    }
    return false;
}

/**
 * Prevent direct URL access to a page
 * @param array $valid_referrers Array of valid referring pages
 * @param string $redirect_url Where to redirect if direct access detected
 * @param string $message Optional error message to display
 */
function prevent_direct_access($valid_referrers, $redirect_url, $message = "Direct access to this page is not allowed.") {
    if (is_direct_access($valid_referrers)) {
        $_SESSION['error_message'] = $message;
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Comprehensive security check for page access
 * Combines multiple checks into one function for ease of use
 * 
 * @param bool $require_login Whether login is required
 * @param bool $require_org Whether organization membership is required
 * @param string $session_flag Session flag to check (optional)
 * @param mixed $flag_value Expected value of the session flag (optional)
 * @param string $redirect_url Where to redirect if check fails
 * @param string $message Optional error message to display
 * @param string $error_prefix Optional prefix for error_type to avoid conflicts
 */
function check_page_access($require_login = true, $require_org = false, $session_flag = null, $flag_value = null, $redirect_url = 'unauthorized.php', $message = "Access denied.", $error_prefix = "") {
    // Check login if required
    if ($require_login && !is_user_logged_in()) {
        $_SESSION['error_message'] = "Please log in to access this page.";
        $_SESSION['error_type'] = !empty($error_prefix) ? $error_prefix . "_auth" : "auth";
        header("Location: login.php");
        exit();
    }
    
    // Check organization if required
    if ($require_org && !user_has_organization()) {
        $_SESSION['error_message'] = "You must belong to an organization to access this feature.";
        $_SESSION['error_type'] = !empty($error_prefix) ? $error_prefix . "_organization" : "organization";
        header("Location: $redirect_url");
        exit();
    }
    
    // Check session flag if provided
    if ($session_flag !== null && $flag_value !== null) {
        if (!isset($_SESSION[$session_flag]) || 
            ($flag_value !== true && $_SESSION[$session_flag] !== $flag_value)) {
            $_SESSION['error_message'] = $message;
            $_SESSION['error_type'] = !empty($error_prefix) ? $error_prefix . "_permission" : "permission";
            header("Location: $redirect_url");
            exit();
        }
    }
}
?> 