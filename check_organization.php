<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user is already part of an organization
$check_user_sql = "SELECT organization_id FROM users WHERE user_id = ?";
$check_user_stmt = $conn->prepare($check_user_sql);
$check_user_stmt->bind_param("i", $user_id);
$check_user_stmt->execute();
$user_result = $check_user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Check if user already has a pending request
$check_pending_sql = "SELECT request_id FROM organization_creation_requests WHERE user_id = ? AND status = 'Pending'";
$check_pending_stmt = $conn->prepare($check_pending_sql);
$check_pending_stmt->bind_param("i", $user_id);
$check_pending_stmt->execute();
$check_pending_result = $check_pending_stmt->get_result();
$has_pending_request = ($check_pending_result->num_rows > 0);

// If the user is already in an organization, set error and redirect to user_org_requests.php
if ($user_data && !empty($user_data['organization_id'])) {
    // Double-check that the organization exists (to handle edge cases)
    $check_org_exists_sql = "SELECT organization_id FROM organizations WHERE organization_id = ?";
    $check_org_exists_stmt = $conn->prepare($check_org_exists_sql);
    $check_org_exists_stmt->bind_param("i", $user_data['organization_id']);
    $check_org_exists_stmt->execute();
    $org_exists_result = $check_org_exists_stmt->get_result();
    
    if ($org_exists_result->num_rows > 0) {
        // User is in an organization - set error and redirect
        $_SESSION['org_request_error'] = "You are already a member of an organization. You must leave your current organization before creating a new one.";
        header("Location: user_org_requests.php");
        exit();
    } else {
        // Organization doesn't exist anymore, update user record
        $update_user_sql = "UPDATE users SET organization_id = NULL, user_type = 'Normal' WHERE user_id = ?";
        $update_user_stmt = $conn->prepare($update_user_sql);
        $update_user_stmt->bind_param("i", $user_id);
        $update_user_stmt->execute();
        
        // Update session
        $_SESSION['organization_id'] = null;
        $_SESSION['user_type'] = 'Normal';
        
        // Redirect directly to create organization request page
        header("Location: create_organization_request.php");
        exit();
    }
} else {
    // Clear organization session data if not in organization
    $_SESSION['organization_id'] = null;
    $_SESSION['user_type'] = 'Normal';
    
    // Check if there's a pending request before redirecting
    if ($has_pending_request) {
        $_SESSION['org_request_error'] = "You already have a pending organization creation request. Please wait for it to be processed.";
        header("Location: user_org_requests.php");
        exit();
    } else {
        // User is not in an organization and has no pending request
        // Redirect directly to create organization request page
        header("Location: create_organization_request.php");
        exit();
    }
}
?> 